<script>
    import OrderArticleList from './OrderArticleList';

    export default {
        components: { OrderArticleList },

        props: ['existingArticles', 'supplierColId'],

        data() {
            return {
                articles: [],
                supplier: null
            }
        },

        computed: {
            hasArticles: function() {
                return _.find(this.articles, function(o) {
                    return parseInt(o.id) > 0;
                });
            },
            articleData: function() {
                return JSON.stringify(this.articles);
            }
        },

        mounted() {
            let that = this;
            if (that.existingArticles.length) {
                $('#dataTableBuilder').on('init.dt', function () {
                    let supplierId = parseInt(_.first(that.existingArticles).supplier_id);
                    window.LaravelDataTables.dataTableBuilder.columns(that.supplierColId).search(supplierId).draw();
                });
            }
        },

        created() {
            if (this.existingArticles.length) {
                this.articles = this.existingArticles;
                this.supplier = _.first(this.articles).supplier_id;
            }
        },

        watch: {
            supplier: function(newVal, oldVal) {
                if (parseInt(newVal) > 0 && typeof window.LaravelDataTables !== 'undefined') {
                    window.LaravelDataTables.dataTableBuilder.columns(this.supplierColId).search(parseInt(newVal)).draw();
                }
            }
        },

        methods: {
            selectArticle: function (id) {
                this.$refs.articleList.selectArticle(id);
            }
        }
    }

</script>