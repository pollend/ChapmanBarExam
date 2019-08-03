import {Component, Vue} from "vue-property-decorator";
import {HydraError} from "../entity/hydra-error";
import {AxiosError} from "axios";
import {ElForm} from "element-ui/types/form";

@Component
export class ValidateMix extends Vue {
    hydraErrorWithNotify(err : AxiosError): boolean {
        if(err.isAxiosError) {
            const error: HydraError = err.response.data;
            this.$notify.error({
                title: error["hydra:title"],
                message: error["hydra:description"]
            });
            return true;
        }
        return false;
    }

    hydraHandleForm(err: AxiosError,form: ElForm) : boolean{
        if(err.isAxiosError) {
            const error: HydraError = err.response.data;
            if(error["@type"] == "ConstraintViolationList") {
                const formItems = (<any>form)['fields'];
                formItems.forEach((it: any) => {
                    for (let v of error.violations) {
                        if(v.propertyPath == it.prop){
                            it.validateMessage = v.message;
                            it.validateState = 'error';
                        }
                    }
                });
                return false;
            }
        }
        return false;
    }
}