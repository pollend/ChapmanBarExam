export default {
    methods: {
        order(o) {
            switch (o) {
                case 'ascending':
                    return 'ASC';
                case 'descending':
                    return 'DESC';
            }
            return '';
        }
    }
}