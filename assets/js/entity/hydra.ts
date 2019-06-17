export interface Hydra {
    "@context": string
    "@id": string
    "@type": string
}

export interface HydraCollection<T extends Hydra> extends Hydra{
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
