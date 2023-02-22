import { t } from 'ttag';

/**
 * <key-value-list> component to handle simple JSON objects with key/values
 */
export default {
    template: `
        <div class="input title text">
            <label :for="name"><: label :></label>
            <div :id="name">
                <div class="key-value-item mb-1" v-for="(item, index) in items">
                    <div>
                        <div>
                            <input type="text" v-model="item.key" @change="onChanged()" :readonly="readonly"/>
                        </div>
                    </div>
                    <div>
                        <div>
                            <input type="text" v-model="item.value" @change="onChanged()" :readonly="readonly"/>
                        </div>
                    </div>
                    <div class="mb-2" v-if="!readonly">
                        <button @click.prevent="remove(index)">${t`Remove`}</button>
                    </div>
                </div>
            </div>

            <button @click.prevent="add" v-if="!readonly">${t`Add`}</button>

            <input type="hidden" :name="name" v-model="result" />
        </div>
    `,

    props: {
        value: String,
        name: String,
        label: String,
        readonly: Boolean,
    },

    data() {
        return {
            items: [],
            result: {},
        }
    },

    created() {
        console.log(this.value);
        let t = this.value;
        if (!this.value) {
            t = null;
        }
        const v = JSON.parse(t);
        if (v) {
            const keys = Object.keys(v);
            keys.forEach((k) => {
                this.items.push({
                    key: k,
                    value: v?.[k] || '',
                })
            });
        }
        if (!this.items.length) {
            this.add();
        }
        this.onChanged();
    },

    methods: {

        onChanged() {
            let obj = {};
            this.items.forEach((item) => {
                obj[item.key] = item?.value || null;
            })
            this.result = JSON.stringify(obj);
            console.log(this.result);
        },

        /**
         * Add an empty item to list.
         *
         * @returns {void}
         */
        add() {
            this.items.push({
                key: '',
                value: '',
            });
        },

        /**
         * Remove item from list.
         *
         * @param {Integer} index The index
         * @returns {void}
         */
        remove(index) {
            this.items.splice(index, 1);
            this.onChanged();
        },
    },
}
