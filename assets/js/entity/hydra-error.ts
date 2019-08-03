export interface Trace {
    args: string[],
    class: string,
    file: string,
    function: string,
    line: number,
    namespace: string,
    short_class: string,
    type: string
}


export interface Violations {
    message: string,
    propertyPath: string
}
export interface HydraError{
    "@content": string,
    "@type": string,
    "hydra:description": string,
    "hydra:title" : string,
    trace?: Trace[]
    violations?: Violations[]

}
