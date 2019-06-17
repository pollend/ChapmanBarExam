import {Hydra} from "./hydra";

export interface Quiz extends Hydra{
    description: string;
    id: number;
    name: string;
}