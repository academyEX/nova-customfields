<template>
    <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0">
        <div class="md:w-1/4 md:py-3">
            <h4 class="font-normal text-80">
                {{ field.name }}
            </h4>
        </div>
        <div class="md:w-3/4 md:py-3 break-all lg:break-words">
            <div class="text-80 mb-2">Main body of passage</div>
            <div class="leading-normal">{{ text }}</div>

            <ul v-if="items.length" class="list-reset">
                <li
                    v-for="(item, index) in items"
                    :key="field.attribute + '.' + index"
                    class="mt-6"
                >
                    <div class="text-80 mb-2">Placeholder {{ index + 1 }} options</div>
                    <div class="leading-normal whitespace-pre">{{ item }}</div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>

export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],
    data: function() {
        return {
            value: JSON.stringify({ text: '', items: []}),
            text: '',
            items: [],
        }
    },
    mounted() {
        const json = (this.field.value && JSON.parse(this.field.value))
            || { text: '', items: [] };
        this.value = JSON.stringify(this.value);
        this.text = json.text || '';
        this.items = json.items || [];
    }
}
</script>
