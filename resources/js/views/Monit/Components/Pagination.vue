<template>
    <b-row>
        <b-col cols="12" lg="4" class="text-center-sm mt-2 d-none d-lg-inline-block" v-if="$parent.totalRows != 0">
            Menampilkan {{ $parent.firstRows }} ke {{ $parent.viewRows }} dari {{ $parent.totalRows }} data
        </b-col>
        <b-col cols="12" lg="8" class="d-flex mt-2" v-if="$parent.totalRows != 0">
            <b-pagination
                class="floom-pagination m-auto m-lg-0 ml-lg-auto"
                :total-rows="$parent.totalRows"
                v-model="$parent.currentPage"
                :per-page="$parent.perPage" />
        </b-col>
        <b-col cols="12 d-flex" v-else>
            
        </b-col>
    </b-row>
</template>

<script>
    export default {
        name: 'pagination'
    }
</script>