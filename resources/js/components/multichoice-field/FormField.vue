<template>
    <DefaultField :field="field" :full-width-content="field.fullWidth" :show-help-text="showHelpText">
        <template #field class="nova-items-field">
            <div class="nova-items-field-input-wrapper flex border border-40 p-4"
                 v-if="field.listFirst == false && ! maxReached">
                <input
                    v-model="newItem"
                    :type="field.inputType"
                    :placeholder="field.placeholder"
                    autocomplete="new-password"
                    @keydown.enter.prevent="addItem"
                    class="flex-1 form-control form-input form-input-bordered"
                />
                <a
                    @click="addItem"
                    v-html="field.createButtonValue"
                    v-if="field.hideCreateButton == false"
                    class="btn btn-default btn-primary ml-3 cursor-pointer font-sans"
                />
            </div>
            <ul ref="novaitemslist" :style="maxHeight" v-if="items.length"
                class="nova-items-field-input-items list-reset border border-40 py-2">
                <li class="flex justify-between pt-2">
                    <div class="ml-4 text-80">Answer</div>
                    <div class="text-80" style="margin-right: 40px;">Correct</div>
                </li>

                <draggable
                    v-model="items"
                    :options="{ disabled: field.draggable == false, handle: '.sortable-handle' }"
                    :item-key="id"
                >
                    <template #item="{element}">
                        <li class="px-4 py-2">
                            <div class="nova-items-field-input-wrapper-item flex py-1">
                            <span v-if="field.draggable" class="sortable-handle py-2 pl-0 pr-4 text-80 cursor-move">
                                |||
                            </span>
                                <div class="flex items-center w-full">
                                    <input
                                        :value="element.value"
                                        :type="field.inputType"
                                        v-on:keyup="updateText(element.id, $event)"
                                        class="flex-1 form-control form-input form-input-bordered"
                                        autocomplete="new-password"
                                        required
                                    />
                                    <input
                                        :checked="element.correct"
                                        type="checkbox"
                                        class="ml-6 mr-1"
                                        @change="updateCorrect(element.id, $event)"
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
            <div class="nova-items-field-input-wrapper flex border border-40 p-4"
                 v-if="field.listFirst && ! maxReached">
                <input
                    v-model="newItem"
                    :type="field.inputType"
                    :placeholder="field.placeholder"
                    class="flex-1 form-control form-input form-input-bordered"
                    @keypress.enter.prevent="addItem"
                />
                <a
                    @click="addItem"
                    v-html="field.createButtonValue"
                    v-if="field.hideCreateButton == false"
                    class="btn btn-default btn-primary ml-3 cursor-pointer"
                />
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
            value: '',
            items: [],
            newItem: '',
            arrayErrors: [],
        }
    },

    methods: {


        setInitialValue() {
            this.value = this.field.value || [];
            const items = this.field.value && JSON.parse(this.field.value) || [];
            this.items = this.fillIds(items);
        },

        fillIds(items) {
            return items.map(item => ({id: uuid(), ...item}));
        },

        stripIds(items) {
            return items.map(item => ({
                value: item.value,
                correct: item.correct,
            }));
        },

        fill(formData) {
            formData.append(this.field.attribute, this.value || [])
        },

        addItem() {
            const item = this.newItem.trim()

            if (item && !this.maxReached) {
                this.items.push({
                    id: uuid(),
                    value: item,
                    correct: false,
                })
                this.newItem = ''

                this.$nextTick(() => {
                    if (this.field.maxHeight) {
                        this.$refs.novaitemslist.scrollTop = this.$refs.novaitemslist.scrollHeight;
                    }
                })
            }
        },

        itemIndex(id) {
            return this.items.findIndex(item => item.id === id)
        },

        updateText(id, event) {
            const index = this.itemIndex(id)
            this.items[index].value = event.target.value;
        },

        updateCorrect(id, event) {
            const index = this.itemIndex(id)
            this.items[index].correct = event.target.checked;
        },

        removeItem(id) {
            const index = this.itemIndex(id)
            this.items.splice(index, 1)
        },

        hasErrors(key) {
            return this.arrayErrors.hasOwnProperty(key);
        },

        getErrorText(key) {
            return this.arrayErrors[key][0];
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
        'items': {
            handler: function (items) {
                this.value = JSON.stringify(this.stripIds(items));
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
