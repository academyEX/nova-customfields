<template>
    <default-field :field="field" :full-width-content="true">
        <template #field class="nova-items-field">
            <template v-for="(item, index) in criteria">
                <table width="100%" class="submission-marking-field">
                    <tr>
                        <th class="font-normal w-1/3">
                            Criteria ({{ item.id }}):
                        </th>
                        <th class="font-normal">Description:</th>
                    </tr>
                    <tr>
                        <td class="submission-marking-field__criteria">
                            <div>{{ item.criteria }}</div>
                        </td>
                        <td class="submission-marking-field__description">
                            <div>{{ item.description }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="font-normal">
                            Feedback: (Mark in {{ languageName }})
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div
                                :id="getEditorId(item)"
                                class="editor-js"
                            ></div>
                        </td>
                    </tr>
                </table>
            </template>
        </template>
    </default-field>
</template>

<style>
.submission-marking-field {
    margin-bottom: 30px;
}

.submission-marking-field:last-child {
    margin-bottom: 0;
}

.submission-marking-field th,
.submission-marking-field td {
    text-align: left;
    vertical-align: top;
    border: 1px solid #bacad6;
    padding: 10px;
}

.submission-marking-field th {
    padding-top: 10px;
}

.submission-marking-field__criteria > div,
.submission-marking-field__description > div {
    white-space: pre-wrap;
}

</style>

<script>
import draggable from "vuedraggable";
import {FormField, HandlesValidationErrors} from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    components: {draggable},

    data: function () {
        return {
            value: "",
            items: [],
            arrayErrors: [],
            criteria: [],
            editorJs: {}, // nova js field
            language: "",
            languageName: ""
        };
    },

    methods: {
        setInitialValue() {
            this.value = this.field.value || "";

            // List of editor JS blocks for each criteria
            // Each has id (key), content (json of editor js content)
            this.items =
                (this.field.value && JSON.parse(this.field.value)) || [];
            // E.g. items: [ { "id": 60, "content": {} } ]

            // list of static criterias.
            // Each has id (key), criteria (mandatory), description (optional), html (html version of current value for readonly)
            // You'll need to manually match id for criteria to items
            // Note: You should treat criteria as the main list to loop through, since items could be empty,
            // and the sort order is controlled by criteria not items.
            // Note 2: You only really need html for readonly detail view
            this.criteria = this.field.criteria || [];

            // Config for editor JS field. Will need to build an editor js for each criteria using this config
            // See vendor/advoor/nova-editor-js/resources/js/components/FormField.vue
            this.editorJs = this.field.editorJs || {};

            this.language = this.field.language || "";

            this.languageName = this.field.languageName || "";

            // Boot all the editors
            this.criteria.forEach(item => this.bootEditor(item));
        },

        /**
         * Boot an editorjs for a given criteria
         * @param item
         */
        bootEditor(item) {
            const self = this;
            const data =
                this.items
                    .filter(value => value.id === item.id)
                    .map(value => value.content)?.[0] || null;
            const holderId = this.getEditorId(item);
            const editor = NovaEditorJS.getInstance(
                {
                    ...this.editorJs.editorSettings,
                    holderId,
                    data,
                    minHeight: 35,
                    onReady: function() {},
                    onChange: function() {
                        editor.save().then(content => {
                            const newValue = {
                                id: item.id,
                                content
                            };

                            // Inject new value via map
                            const items = self.items.map(value =>
                                value.id === item.id ? newValue : value
                            );

                            // In case the item wasn't in the list, add it
                            if (!items.find(value => value.id === item.id)) {
                                items.push(newValue);
                            }

                            // Save to value
                            self.items = items;
                            self.value = JSON.stringify(items);
                        });
                    }
                },
                this.editorJs
            );
        },

        fill(formData) {
            formData.append(this.field.attribute, this.value || []);
        },

        getEditorId(item) {
            return `editor-js-${this.field.attribute}-${item.id}`;
        }
    },
    computed: {},
    watch: {
        errors: {
            handler: function (errors) {
                if (errors.errors.hasOwnProperty(this.field.attribute)) {
                    this.arrayErrors = errors.errors[this.field.attribute][0];
                }
            },
            deep: true
        }
    }
};
</script>
