import {Hydra} from "./hydra";
import Classroom from "./classroom";

export default interface QuizAccess  extends Hydra{
    id: number;
    numAttempts: number;
    openDate: string;
    closeDate: string;
    isHidden: boolean;
    classroom: string | Classroom;
    quiz: string | QuizAccess;
}