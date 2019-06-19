import {Hydra} from "./hydra";
import {Timestamp} from "./timestamp";

export interface Quiz extends Hydra, Timestamp{
    description: string;
    id: number;
    name: string;
    numquestions: number,

}