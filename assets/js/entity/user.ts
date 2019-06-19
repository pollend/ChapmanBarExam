import {Hydra} from "./hydra";

export const USER_TYPE = "user";

export default interface User extends Hydra {
    id: number;
    email: string;
    roles: string[];
    createdAt: string;
    updatedAt: string;
};