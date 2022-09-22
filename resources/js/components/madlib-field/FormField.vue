<template>
    <DefaultField :field="field" :full-width-content="field.fullWidth" :show-help-text="showHelpText">
        <template #field class="nova-items-field">
            <ul ref="novaitemslist" :style="maxHeight"
                class="nova-items-field-input-items list-reset border border-40 py-2">
                <li class="px-4 py-2">
                    <div class="text-80 mb-2">Enter main body of passage, with __ as placeholders</div>
                    <textarea
                        v-model="text"
                        type="text"
                        v-on:keyup="updateText($event)"
                        :class="{'border-danger': hasErrors(field.attribute)}"
                        class="w-full form-control form-input form-input-bordered py-3 h-auto"
                        autocomplete="new-password"
                        required
                    />
                </li>

                <draggable
                    v-model="items"
                    :options="{ disabled: field.draggable == false, handle: '.sortable-handle' }"
                    :item-key="id"
                >
                    <template #item="{element}">
                        <li class="px-4 py-2">
                            <div class="text-80" v-html="'Options for placeholder ' + (itemIndex(element.id) + 1)"/>
                            <div class="nova-items-field-input-wrapper-item flex py-1">
                            <span v-if="field.draggable" class="sortable-handle py-2 pl-0 pr-4 text-80 cursor-move">
                                |||
                            </span>
                                <div class="flex items-center w-full">
                                <textarea
                                    :value="element.text"
                                    :type="field.inputType"
                                    v-on:keyup="updateItem(element.id, $event)"
                                    class="w-full form-control form-input form-input-bordered py-3 h-auto"
                                    autocomplete="new-password"
                                    required
                                />
                                    <span
                                        @click="removeItem(element.id)"
                                        style="font-size: 32px;"
                                        class="ml-4 mr-2 font-thin cursor-pointer hover:font-normal"
                                        v-html="field.deleteButtonValue"
                                    />
                                </div>
                            </div>
                        </li>
                    </template>
                </draggable>
            </ul>
            <div v-if="hasErrors(field.attribute)" class="help-text help-text-error -mt-2 text-danger py-2">
                <p v-html="getErrorText(field.attribute)"/>
            </div>
        </template>
    </DefaultField>
</template>

<style scoped>
.sortable-chosen {
    border: 1px solid var(--50);
    background-color: var(--20);
    box-shadow: 2px 2px 2px var(--40);
    margin-left: -5px;
}

.sortable-handle {
    -o-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
    -webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    transform: rotate(90deg);
    position: relative;
    left: -5px;
    top: 5px;
    height: 38px;
}
</style>

<script>
import draggable from 'vuedraggable'
import {v4 as uuid} from 'uuid'
import {FormField, HandlesValidationErrors} from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    components: {draggable},

    data: function () {
        return {
            value: JSON.stringify({text: '', items: []}),
            text: '',
            items: [],
            arrayErrors: [],
        }
    },

    methods: {
        setInitialValue() {
            const json = (this.field.value && JSON.parse(this.field.value))
                || {text: '', items: []};
            this.value = JSON.stringify(this.value);
            this.text = json.text || '';
            this.items = [];
            if (json.items) {
                for (const item of json.items) {
                    this.items.push({
                        id: uuid(),
                        text: item,
                    })
                }
            }
        },

        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        addItem() {
            if (!this.maxReached) {
                this.items.push({
                    id: uuid(),
                    text: ''
                });

                this.$nextTick(() => {
                    if (this.field.maxHeight) {
                        this.$refs.novaitemslist.scrollTop = this.$refs.novaitemslist.scrollHeight;
                    }
                })
            }
        },

        updateText(event) {
            // Ensure we have the correct number of blocks
            const number = (event.target.value.match(/__/g) || []).length;
            while (this.items.length < number) {
                this.items.push({
                    id: uuid(),
                    text: '',
                });
            }
        },

        updateItem(id, event) {
            const index = this.itemIndex(id)
            if (index >= 0) {
                this.items[index]['text'] = event.target.value;
            }
        },

        removeItem(id) {
            const index = this.itemIndex(id)
            if (index >= 0) {
                this.items.splice(index, 1)
            }
        },

        hasErrors(key) {
            return this.arrayErrors.hasOwnProperty(key);
        },

        getErrorText(key) {
            return this.arrayErrors[key][0];
        },

        itemIndex(id) {
            return this.items.findIndex(item => item.id === id)
        },
    },
    computed: {
        maxHeight() {
            if (this.field.maxHeight == false) {
                return '';
            }

            return `max-height: ${this.field.maxHeight}px; overflow: auto;`;
        },
        maxReached() {
            return this.field.max !== false && this.items.length + 1 > this.field.max;
        }
    },
    watch: {
        'text': {
            handler: function (text) {
                this.value = JSON.stringify({
                    text: text,
                    items: this.items.map(item => item.text)
                });
            },
        },
        'items': {
            handler: function (items) {
                this.value = JSON.stringify({
                    text: this.text,
                    items: items.map(item => item.text)
                });
            },
            deep: true
        },
        'errors': {
            handler: function (errors) {
                if (errors.errors.hasOwnProperty(this.field.attribute)) {
                    this.arrayErrors = errors.errors;
                }
            },
            deep: true
        }
    }
}
</script>
