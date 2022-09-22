<template>
    <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0">
        <div class="md:w-1/4 md:py-3">
            <h4 class="font-normal text-80">
                {{ field.name }}
            </h4>
        </div>
        <div class="md:w-3/4 md:py-3 break-all lg:break-words">
            <template v-for="(item, index) in criteria">
                <table width="100%" class="submission-marking-detail">
                    <tr>
                        <th class="font-normal">Criteria ({{ item.id }}):</th>
                        <th class="font-normal">Description:</th>
                    </tr>
                    <tr>
                        <td class="submission-marking-detail__criteria w-1/3">
                            <div>{{ item.criteria }}</div>
                        </td>
                        <td class="submission-marking-detail__description">
                            <div>{{ item.description }}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2" class="font-normal">
                            Feedback: (Marked in {{ languageName }})
                        </th>
                    </tr>
                    <tr>
                        <td
                            class="submission-marking-detail__feedback"
                            colspan="2"
                            v-html="item.html"
                        ></td>
                    </tr>
                </table>
            </template>
        </div>
    </div>
</template>

<style>
.submission-marking-detail {
    margin-bottom: 30px;
}

.submission-marking-detail:last-child {
    margin-bottom: 0;
}

.submission-marking-detail th,
.submission-marking-detail td {
    text-align: left;
    vertical-align: top;
    border: 1px solid #bacad6;
    padding: 10px;
}

.submission-marking-detail th {
    padding-top: 10px;
}

.submission-marking-detail__criteria > div,
.submission-marking-detail__description > div {
    white-space: pre-wrap;
}
</style>

<script>
export default {
    props: ["resource", "resourceName", "resourceId", "field"],
    data: function() {
        return {
            criteria: [],
            language: "",
            languageName: ""
        };
    },
    mounted() {
        this.criteria = this.field.criteria || [];
        this.language = this.field.language || "";
        this.languageName = this.field.languageName || "";
    }
};
</script>
