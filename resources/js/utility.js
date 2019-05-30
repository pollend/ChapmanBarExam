import _ from 'lodash';
export default {

    /**
     * Maps response data to the datatable entries in a key lookup
     * @param payload
     * @param mappings
     * @returns {Array}
     */
    mapDatatable: function (payload,mappings) {
        let keys = _.values(mappings);
        let invertMappings = _.invertBy(mappings)
        return _.map(payload, item => _.mapKeys(_.pick(item, keys), (value, key) => invertMappings[key]))
    }
}