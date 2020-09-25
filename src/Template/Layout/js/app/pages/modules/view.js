/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

export default {
    components: {
        PropertyView: () => import(/* webpackChunkName: "property-view" */'app/components/property-view/property-view'),
        HorizontalTabView: () => import(/* webpackChunkName: "horizontal-tab-view" */'app/components/horizontal-tab-view'),
    },

    props: {
        object: Object,
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,
        };
    },

    mounted() {
        window.addEventListener('keydown', this.toggleTabs);

        // manually trigger changes for cloned data
        if (window.location.pathname.includes('/clone')) {
            Object.keys(this.object.attributes).forEach((key) => {
                if (this.object.attributes[key]) {
                    this.$el.dispatchEvent(new CustomEvent('change', {
                        bubbles: true,
                        detail: {
                            id: key,
                            isChanged: true,
                        }
                    }));
                }
            });
        }
    },

    methods: {
        toggleTabs(e) {
            let key = e.which || e.keyCode || 0;
            if(key === 27) {
                return this.tabsOpen = !this.tabsOpen;
            }
        },

        async translateAll(data, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');
            try {
                await Promise.all(
                    Object.keys(data).map(key =>
                        this.fetchTranslation(data[key])
                    )
                );
            } catch (error) {
                alert(error);
                console.log(error);
            }
            el.classList.remove('is-loading-spinner');
        },

        translate(object, e) {
            const el = e.currentTarget;
            el.classList.add('is-loading-spinner');

            this.fetchTranslation(object)
                .catch((error) => {
                    alert(error);
                    console.log(error);
                })
                .finally(() => {
                    el.classList.remove('is-loading-spinner');
                });
        },

        fetchTranslation(object) {
            if (!object.content) {
                return;
            }
            if (!object.to) {
                // use `value` from select on new translations
                object.to = this.$refs.translateTo.value;
            }

            return this.$helpers.autoTranslate(object.content, object.from, object.to)
                .catch(r => {
                    throw new Error(`Unable to translate field ${object.field}`);
                })
                .then(r => {
                    if (!r.translation) {
                        throw new Error(`Unable to translate field ${object.field}`);
                    }

                    const field = object.field.replaceAll('_', '-');
                    const ckfield = `translated-fields-${field}`;
                    if (CKEDITOR.instances && CKEDITOR.instances[ckfield]) {
                        CKEDITOR.instances[ckfield].setData(r.translation);
                    } else {
                        this.$refs[object.field].value = r.translation;
                    }
                });
        },
    }
}
