
import {CurrentPageFilter, FilterBuilder, ItemsPerPageFilter, Sort, SortFilter} from "./filter";

export function mapSort(sort:string) : Sort{
    switch (sort) {
        case 'ascending':
            return Sort.Ascending;
        case 'descending':
            return Sort.Descending;
    }
    return Sort.None;
}

export function buildSortQueryForVueDataTable(sortQuery:any) {

    const builder = new FilterBuilder();

    builder.addFilter(new ItemsPerPageFilter(sortQuery.pageSize));
    builder.addFilter(new CurrentPageFilter(sortQuery.page));
    if(sortQuery.sort && sortQuery.sort.prop && sortQuery.sort.order){
        const order= mapSort(sortQuery.sort.order);
        builder.addFilter(new SortFilter(sortQuery.sort.prop,order));
    }
    return builder;
}