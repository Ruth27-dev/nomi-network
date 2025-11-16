<template x-if="table?.paginate">
    <div class="pagination">
        <div class="pagination-left">
            <span class="!text-[12px]">Total of : <span x-text="table.paginate.totalPages"></span> </span>
            <span class="!text-[12px]">Go to</span>
            <div class="form-row">
                <input type="text" x-bind:value="table.paginate.currentPage" x-ref="paginateRef">
            </div>
            <button type="button" @click="table.goToPage($refs.paginateRef.value)">Go</button>
        </div>
        <div class="pagination-right">
            <div class="pagination-wrapper">
                <div class="pagination-item left" :class="{ disabled: table.paginate.currentPage == 1 }"
                    @click="table.goPreviousPage()">
                    <i data-feather="chevron-left"></i>
                </div>
                <template x-for="item in table.paginate.leftSection">
                    <div class="pagination-item" :class="{ active: table.paginate.currentPage == item }"
                        @click="table.goToPage(item)">
                        <span x-text="item"></span>
                    </div>
                </template>
                <template x-if="table.paginate.leftSection?.length == 1">
                    <div class="pagination-item disabled">
                        <i data-feather="more-horizontal"></i>
                    </div>
                </template>
                <template x-for="item in table.paginate.midSection">
                    <div class="pagination-item" :class="{ active: table.paginate.currentPage == item }"
                        @click="table.goToPage(item)">
                        <span x-text="item"></span>
                    </div>
                </template>
                <template x-if="table.paginate.rightSection?.length == 1">
                    <div class="pagination-item disabled">
                        <i data-feather="more-horizontal"></i>
                    </div>
                </template>
                <template x-for="item in table.paginate.rightSection">
                    <div class="pagination-item" :class="{ active: table.paginate.currentPage == item }"
                        @click="table.goToPage(item)">
                        <span x-text="item"></span>
                    </div>
                </template>
    
                <div class="pagination-item right"
                    :class="{ disabled: table.paginate.currentPage == table.paginate.totalPages }" @click="table.goNextPage()">
                    <i data-feather="chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
</template>
