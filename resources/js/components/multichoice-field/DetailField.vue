<template>
    <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0">
        <div class="md:w-1/4 md:py-3">
            <h4 class="font-normal text-80">
                {{ field.name }}
            </h4>
        </div>
        <div class="md:w-3/4 md:py-3 break-all lg:break-words">
            <ul v-if="items.length" class="list-reset border border-40">
                <li class="flex justify-between py-3">
                    <div class="ml-4 text-80">Question</div>
                    <div class="text-80 mr-4">Correct</div>
                </li>
                <li
                    v-for="(item, index) in items"
                    :key="field.attribute + '.' + index"
                    class="px-4 py-2 border-t border-40"
                >
                    <div class="flex py-1">
                        <div class="flex items-center w-full">
                            <div class="flex-1">{{ item.value }}</div>
                            <input
                                :checked="item.correct"
                                type="checkbox"
                                class="ml-6 mr-1"
                                disabled
                            />
                        </div>
                    </div>
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
            items: [],
        }
    },
    mounted() {
        this.items = (this.field.value && JSON.parse(this.field.value)) || [];
    }
}
</script>
