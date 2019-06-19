import {Hydra} from "./hydra";
import QuizAccess from "./quiz-access";

export default interface Classroom extends Hydra{
    id: number,
    name: string,
    description: string,
    courseNumber: string,
    quizAccess: QuizAccess[] | string,
}