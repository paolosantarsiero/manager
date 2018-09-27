<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Trash can controller: list, restore & remove permanently objects
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class TrashController extends AppController
{

    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->loadComponent('Properties');

        $this->Modules->setConfig('currentModuleName', 'trash');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event) : void
    {
        parent::beforeFilter($event);

        $actions = [
            'restore', 'delete',
        ];

        if (in_array($this->request->params['action'], $actions)) {
            // for csrf
            $this->getEventManager()->off($this->Csrf);

            // for security component
            $this->Security->setConfig('unlockedActions', $actions);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);
        $this->set('moduleLink', ['_name' => 'trash:list']);
    }

    /**
     * Display deleted resources list.
     *
     * @return \Cake\Http\Response|null
     * @codeCoverageIgnore
     */
    public function index() : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObjects('trash', $this->request->getQueryParams());
        } catch (BEditaClientException $e) {
            // Error! Back to dashboard.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('objects', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('types', ['right' => $this->Modules->objectTypes(false)]);

        $this->set('properties', $this->Properties->indexList('trash'));

        return null;
    }

    /**
     * View single deleted resource.
     *
     * @param mixed $id Resource ID.
     * @return \Cake\Http\Response|null
     * @codeCoverageIgnore
     */
    public function view($id) : ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, 'trash');
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'trash:list']);
        }

        $object = $response['data'];
        $schema = $this->Schema->getSchema($object['type']);

        $this->set(compact('object', 'schema'));
        $this->set('properties', $this->Properties->viewGroups($object, 'trash'));

        return null;
    }

    /**
     * Restore resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function restore() : ?Response
    {
        $this->request->allowMethod(['post']);
        $ids = [];
        if (!empty($this->request->getData('ids'))) {
            $ids = $this->request->getData('ids');
            if (is_string($ids)) {
                $ids = explode(',', $this->request->getData('ids'));
            }
        } else {
            $ids = [$this->request->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->restoreObject($id, 'objects');
            } catch (BEditaClientException $e) {
                // Error! Back to object view.
                $this->log($e, LogLevel::ERROR);
                $this->Flash->error($e, ['params' => $e->getAttributes()]);
                if (!empty($this->request->getData('id'))) {
                    return $this->redirect(['_name' => 'trash:view', 'id' => $this->request->getData('id')]);
                }

                return $this->redirect(['_name' => 'trash:view', 'id' => $id]);
            }
        }

        $this->Flash->success(__('Object(s) restored'));

        $query = $this->request->getData('query');
        if (!empty($query)) {
            $query = htmlspecialchars_decode($query);
            $query = unserialize($query);

            return $this->redirect(['_name' => 'trash:list'] + $query);
        }

        return $this->redirect(['_name' => 'trash:list']);
    }

    /**
     * Permanently delete resource.
     *
     * @return \Cake\Http\Response|null
     */
    public function delete() : ?Response
    {
        $this->request->allowMethod(['post']);
        $ids = [];
        if (!empty($this->request->getData('ids'))) {
            $ids = $this->request->getData('ids');
            if (is_string($ids)) {
                $ids = explode(',', $this->request->getData('ids'));
            }
        } else {
            $ids = [$this->request->getData('id')];
        }
        foreach ($ids as $id) {
            try {
                $this->apiClient->remove($id);
            } catch (BEditaClientException $e) {
                // Error! Back to object view.
                $this->log($e, LogLevel::ERROR);
                $this->Flash->error($e, ['params' => $e->getAttributes()]);

                if (!empty($this->request->getData('id'))) {
                    return $this->redirect(['_name' => 'trash:view', 'id' => $this->request->getData('id')]);
                }

                return $this->redirect(['_name' => 'trash:view', 'id' => $id]);
            }
        }

        $this->Flash->success(__('Object deleted from trash'));

        $query = $this->request->getData('query');
        if (!empty($query)) {
            $query = htmlspecialchars_decode($query);
            $query = unserialize($query);

            return $this->redirect(['_name' => 'trash:list'] + $query);
        }

        return $this->redirect(['_name' => 'trash:list']);
    }

    /**
     * Permanently delete multiple data.
     * If filter type is active, empty trash by type
     *
     * @return \Cake\Http\Response|null
     */
    public function empty() : ?Response
    {
        $this->request->allowMethod(['post']);

        $query = [];
        $q = $this->request->getData('query');
        if (!empty($q)) {
            $q = htmlspecialchars_decode($q);
            $q = unserialize($q);
            if (!empty($q['filter'])) {
                $query['filter'] = $q['filter'];
            }
        }

        // cycle over trash results
        $response = $this->apiClient->getObjects('trash', $query);
        $counter = 0;
        while (!empty($response['meta']['pagination']['count']) && $response['meta']['pagination']['count'] > 0) {
            foreach ($response['data'] as $index => $data) {
                try {
                    $this->apiClient->remove($data['id'], $query);
                    $counter++;
                } catch (BEditaClientException $e) {
                    // Error! Back to trash index.
                    $this->log($e, LogLevel::ERROR);
                    $this->Flash->error($e, ['params' => $e->getAttributes()]);

                    return $this->redirect(['_name' => 'trash:index'] + $query);
                }
            }
            $response = $this->apiClient->getObjects('trash', $query);
        }
        $this->Flash->success(__(sprintf('%d objects deleted from trash', $counter)));

        if (!empty($query)) {
            return $this->redirect(['_name' => 'trash:list'] + $query);
        }

        return $this->redirect(['_name' => 'trash:list']);
    }
}
