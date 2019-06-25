import {Component, Vue} from "vue-property-decorator";

export interface Hydra {
    "@context": string
    "@id": string
    "@type": string
}

export interface HydraCollection<T extends Hydra | {}> extends Hydra{
    "hydra:member": T[]
    "hydra:totalItems" : number,
    "hydra:view": {
        "@id": string,
        "@type": string,
        "hydra:first"?: string,
        "hydra:last"?: string,
        "hydra:next"?: string,
    }
}

@Component
export class HydraMixxin extends Vue {
    hydraID(target: Hydra | string): string {
        if (target instanceof String) {
            return <string>target;
        } else {
            const hy: Hydra = <Hydra>target;
            return hy["@id"];
        }
    }

    checkHydraMatch(c1: any, c2: any): boolean {
        return this.hydraID(c1) == this.hydraID(c2);
    }
}


export function hydraID(target: any) : string {
    if(target instanceof String){
        return <string>target;
    }else {
        const hy: Hydra = target;
        return hy["@id"];
    }

}
