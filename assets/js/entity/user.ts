import {Hydra} from "./hydra";
import QuizSession from "./quiz-session";

export const USER_TYPE = "user";

export default interface User extends Hydra {
    id: number;
    email: string;
    roles: string[];
    createdAt: string;
    updatedAt: string;
    quizSessions: QuizSession[]
};
