import {filter} from "minimatch";
import  _ from 'lodash';

export interface op {
    key:string;
    value:string;
}

export interface FilterOps{
    apply(builder: FilterBuilder) : op;
}

export class ExistFilter implements FilterOps{
    prop: string;
    exist: boolean;

    constructor(prop: string, exist: boolean) {
        this.prop = prop;
        this.exist = exist;
    }

    apply(builder: FilterBuilder ) {
        return {key: this.prop + '[exists]', value: this.exist ? 'true' : 'false'};
    }
}




export class SearchFilter implements FilterOps {
    prop: string;
    target:string;

    constructor(prop:string, target: string){
        this.prop = prop;
        this.target = target;
    }

    apply(builder: FilterBuilder) {
        return {key: this.prop, value: this.target};
    }
}


export enum Sort {
    None = 0,
    Ascending,
    Descending,
}

export class SortFilter implements FilterOps {
    prop: string;
    sort: Sort;
    arg: string;

    constructor(prop: string, sort: Sort, arg: string = 'order') {
        this.prop = prop;
        this.sort = sort;
        this.arg = arg;
    }

    apply(builder: FilterBuilder): op {
        let order: string;
        switch (this.sort) {
            case Sort.None:
                order = '';
                break;
            case Sort.Ascending:
                order = 'asc';
                break;
            case Sort.Descending:
                order = 'desc';
                break;
        }

        return {key: this.arg + '[' + this.prop + ']', value: order};
    }

}

export class CurrentPageFilter implements FilterOps {
    page: number;

    constructor(page: number) {
        this.page = page;
    }

    apply(builder: FilterBuilder): op {
        return {key: 'page', value: this.page + ''};
    }
}


export class ItemsPerPageFilter implements FilterOps{
    numberItem: number;
    constructor(numberItems: number){
        this.numberItem = numberItems;
    }

    apply(builder: FilterBuilder): op {
        return {key: 'itemsPerPage', value: this.numberItem +''};
    }
    
}

export class FilterBuilder {
    filters: FilterOps[];

    constructor() {
        this.filters = [];
    }

    addFilter(filter: FilterOps) {
        this.filters.push(filter);
        return this;
    }

    build() {
        let result: op[] = [];
        this.filters.forEach((f) => {
            const o: op = f.apply(this);
            o.key = o.key.trim();
            o.value = o.value.trim();
            result.push(o);
        });

        let payload: string[] = [];

        _.each(_.groupBy(result, (e) => {
            return e.key;
        }), (value, key) => {
            if (value.length == 1) {
                let o: op = value[0];
                payload.push(key + '=' + o.value);
            } else {
                if (key.endsWith('[]')) {
                    value.forEach((r) => {
                        payload.push(key + '=' + r.value);
                    });
                } else if (key.endsWith(']')) {
                    throw new Error('problematic end matching');
                } else {
                    value.forEach((r) => {
                        payload.push(key + '[]=' + r.value);
                    });
                }

            }

        });

        return payload.join('&');
    }
}
