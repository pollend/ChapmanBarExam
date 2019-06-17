<script lang="ts">

    import {Component, Vue} from "vue-property-decorator";

@Component
export default class Redirect extends Vue {
    beforeCreated() {
        const {params, query} = this.$route;
        const {path} = params;
        this.$router.replace({path: '/' + path, query});
    }
}
//
// export default {
//   beforeCreate() {
//     const { params, query } = this.$route;
//     const { path } = params;
//     this.$router.replace({ path: '/' + path, query });
//   },
//   render: function(h) {
//     return h(); // avoid warning message
//   },
// };
</script>
