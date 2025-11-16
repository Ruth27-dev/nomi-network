/**
 *
 * @param {string} url
 * @returns
 */
const TableGroup = function (url) {
    return {
        /**
         * table data
         * @var {Array}
         */
        data: null,
        /**
         * table paginates
         * @var {Object}
         */
        paginate: null,
        /**
         * table loading
         * @var {boolean}
         */
        loading: false,
        /**
         * load tale data
         * @param {*} params
         */
        init: function (params = {}) {
            /**
             * exists param after reload pages
             */
            let coreParams = {};
            location.search
                .replace("?", "")
                .split("&")
                .map((item) => {
                    const [key, value] = item.split("=");
                    if (!key || !value) return;
                    coreParams[key] = value;
                });
            /**
             * combine params
             */
            params = {
                ...coreParams,
                ...params,
            };
            this.fetchData(params, (response) => {
                let paginatedData = response;
                let otherData = null;

                // Handle wrapped response: { data: LaravelPaginationObj, otherData: ... }
                if (response?.data && response?.data?.data !== undefined) {
                    paginatedData = response.data;
                    otherData = response.otherData;
                }

                const {
                    data,
                    total,
                    per_page,
                    current_page,
                    last_page,
                } = paginatedData;

                this.otherData = otherData ?? response.otherData;

                this.paginate = {
                    totalItems: total,
                    currentPage: current_page,
                    perPage: per_page,
                    totalPages: last_page,
                    leftSection: function () {
                        if (last_page > 10) {
                            if (current_page >= 4) {
                                return [1];
                            } else {
                                let arr = [];
                                for (let i = 1; i <= (total > 4 ? 4 : last_page); i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        }
                    },
                    midSection: function () {
                        if (last_page > 10) {
                            if (current_page >= 4 && current_page < last_page - 2) {
                                let arr = [];
                                for (let i = current_page - 1; i <= current_page + 1; i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        } else {
                            let arr = [];
                            for (let i = 1; i <= last_page; i++) {
                                arr.push(i);
                            }
                            return arr;
                        }
                    },
                    rightSection: function () {
                        if (last_page > 10) {
                            if (current_page < last_page - 2) {
                                return [last_page];
                            } else {
                                let arr = [];
                                for (let i = last_page - 3; i <= last_page; i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        }
                    },
                };

                this.data = data?.map((item, index) => {
                    return {
                        iteration:
                            this.paginate.currentPage * this.paginate.perPage -
                            this.paginate.perPage +
                            (index + 1),
                        ...item,
                    };
                });

                setTimeout(() => {
                    feather.replace();
                }, 10);
            });

        },
        /**
         * fetch data table
         * @param {*} params
         * @param {*} callback
         */
        fetchData: function (params, callback) {
            /**
             * combine core param with manual params
             */
            this.loading = true;
            Axios({
                url: url,
                method: "GET",
                params: params,
            })
                .then((response) => {
                    callback(response.data);
                    /**
                     * Replace state after finished fetching data
                     */
                    let paramString = "";
                    Object.entries(params)?.map(([key, value], index) => {
                        if (index == 0) paramString += "?";
                        paramString += `${key}=${value}`;
                        if (Object.entries(params).length - 1 > index)
                            paramString += "&";
                    });
                    history.replaceState(
                        {},
                        "",
                        paramString || location.pathname
                    );
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        /**
         * reload current data
         */
        reload: function () {
            this.init();
        },
        /**
         * reload with clear params
         */
        reset: function (callback) {
            this.fetchData({}, (response) => {
                let paginatedData = response;
                let otherData = null;

                // Detect wrapped response format
                if (response?.data && response?.data?.data !== undefined) {
                    paginatedData = response.data;
                    otherData = response.otherData;
                }

                const {
                    data,
                    total,
                    per_page,
                    current_page,
                    last_page,
                } = paginatedData;

                this.otherData = otherData ?? response.otherData;

                this.paginate = {
                    totalItems: total,
                    currentPage: current_page,
                    perPage: per_page,
                    totalPages: last_page,
                    leftSection: function () {
                        if (last_page > 10) {
                            if (current_page >= 4) {
                                return [1];
                            } else {
                                let arr = [];
                                for (let i = 1; i <= (total > 4 ? 4 : last_page); i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        }
                    },
                    midSection: function () {
                        if (last_page > 10) {
                            if (current_page >= 4 && current_page < last_page - 2) {
                                let arr = [];
                                for (let i = current_page - 1; i <= current_page + 1; i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        } else {
                            let arr = [];
                            for (let i = 1; i <= last_page; i++) {
                                arr.push(i);
                            }
                            return arr;
                        }
                    },
                    rightSection: function () {
                        if (last_page > 10) {
                            if (current_page < last_page - 2) {
                                return [last_page];
                            } else {
                                let arr = [];
                                for (let i = last_page - 3; i <= last_page; i++) {
                                    arr.push(i);
                                }
                                return arr;
                            }
                        }
                    },
                };

                this.data = data?.map((item, index) => {
                    return {
                        iteration:
                            this.paginate.currentPage * this.paginate.perPage -
                            this.paginate.perPage +
                            (index + 1),
                        ...item,
                    };
                });

                if (callback) callback(response);

                setTimeout(() => {
                    feather.replace();
                }, 10);
            });
        },

        /**
         * @returns {boolean}
         */
        empty: function () {
            return this.data?.length === 0;
        },
        /**
         * go to previous page
         * @returns
         */
        goPreviousPage: function () {
            if (this.paginate.currentPage <= 1) return;
            this.paginate.currentPage -= 1;
            this.init({
                page: this.paginate.currentPage,
            });
        },
        /**
         * go to next page
         * @returns
         */
        goNextPage: function () {
            if (this.paginate.currentPage >= this.paginate.totalPages) return;
            this.paginate.currentPage += 1;
            this.init({
                page: this.paginate.currentPage,
            });
        },
        /**
         * go to specific page
         * @param {*} page
         * @returns
         */
        goToPage: function (page) {
            if (
                isNaN(page) ||
                page < 1 ||
                page > this.paginate.totalPages ||
                page == this.paginate.currentPage
            )
                return;
            this.paginate.currentPage = page;
            this.init({
                page: this.paginate.currentPage,
            });
        },
    };
};

export default TableGroup;
