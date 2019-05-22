import _ from 'lodash';
export default {

    mapDatatable: function (payload,mappings) {
        let keys = _.values(mappings);
        let invertMappings = _.invertBy(mappings)
        return _.map(payload, item => _.mapKeys(_.pick(item, keys), (value, key) => invertMappings[key]))
    }
}