import {Hydra} from "./hydra";
import {Quiz} from "./quiz";

export default interface UserQuizAccess extends Hydra {
    id: number,
    quiz: string | Quiz;
    hidden: boolean,
    owner: string,
    userAttempts: number,
    numAttempts: number
}