<template>
    <div class="whitelist-container" v-if="classroom">
        <el-table  v-loading="loading" ref="emailTable"  :data="emails" height="400"  @selection-change="handleSelectionChange">
            <el-table-column
                    type="selection"
                    width="55">
            </el-table-column>
            <el-table-column label="Email">
                <template slot-scope="scope">
                    <span :class="{'email-marked': mark.includes(scope.row)}">{{scope.row}}</span>
                </template>
            </el-table-column>
            <el-table-column width="120">
                <template slot="header" slot-scope="scope">
                    <el-button v-if="selectedEmails.length > 0"
                            @click.native.prevent="remove(selectedEmails)"
                            type="text">
                        Remove
                    </el-button>
                </template>

            </el-table-column>
        </el-table>
        <el-row :gutter="20" class="bottom-add">
            <el-col :span="20"><el-input v-model="email"></el-input></el-col>
            <el-col :span="4">
                <el-button @click="add(email)">Add</el-button> <el-divider direction="vertical"></el-divider>
                <el-button @click="save()" type="success" :disabled="!hasChanged" :loading="loading">Save</el-button></el-col>
        </el-row>

<!-- TODO:// Think about bulk insert method for functionality -->
<!--        <div>-->
<!--            <el-button @click="bulkImport = !bulkImport">Import Email List</el-button>-->
<!--            <el-button>Instructions</el-button>-->
<!--        </div>-->

        <el-form label-width="120px">
            <h2>Bulk Insert</h2>
            <el-form-item label="Instructions">
                Bulk Insert allows adding more then one whitelisted email. emails either have to be comma or separated by a carriage return.
            </el-form-item>
            <el-form-item label="Emails">
                <el-input
                        type="textarea"
                        :rows="2"
                        placeholder="Please input"
                        v-model="bulkEmails">
                </el-input>
            </el-form-item>
            <el-button  @click="addBulkEmails()" >Add Emails</el-button>
        </el-form>

    </div>
</template>

<script lang="ts">

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import {namespace} from "vuex-class";
import Classroom from "../../../entity/classroom";
import service from "../../../utils/request";
import {HydraCollection} from "../../../entity/hydra";
import _ from "lodash";

const classroomShowModule = namespace('dashboard/classroom/show');

@Component
export default class ClassroomWhitelist extends Vue {
    @Provide() collection: HydraCollection<string> = null;
    @Provide() email: string = "";
    @Provide() selectedEmails: string[] = [];
    @Provide() hasChanged: boolean = false;
    @Provide() loading: boolean = false;
    @Provide() bulkEmails: string = '';
    @Provide() mark: string[] = [];


    @classroomShowModule.Getter('classroom') classroom: Classroom;
    async loadCollection() {
        this.loading = true;
        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/whitelist',
            method: 'GET'
        });
        this.loading =false;
        this.collection = response.data
    }

    async created() {
        await this.loadCollection();
    }

    handleSelectionChange(val: string[]) {
        this.selectedEmails = val;
    }

    async addBulkEmails(){
        for(let e of this.bulkEmails.split('\n')){
            this.mark.push(e);
            this.collection["hydra:member"].push(e);
            this.bulkEmails = ''
        }

    }

    async save() {
        this.loading = true;
        const response = await service({
            url: '/_api/classrooms/' + this.classroom.id + '/whitelist',
            method: 'PUT',
            data: {
                'emails': this.collection["hydra:member"]
            }
        });
        this.loading = false;

        this.collection = response.data;
    }

    get emails() {
        return this.collection ? this.collection['hydra:member'].sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase())) : [];
    }

    remove(email: string | string[]) {
        if (Array.isArray(email)) {
            for (let e of email) {
                this.remove(e);
            }
        } else {
            this.collection["hydra:member"] = _.remove(this.collection["hydra:member"], (n) => n != email);
        }
        this.hasChanged = true;
    }

    add(email: string) {
        this.mark.push(email);
        this.collection["hydra:member"].push(email);
        this.email = "";
        this.hasChanged = true;
    }


}
</script>

<style lang="scss">
    .whitelist-container {
        .bottom-add{
            margin: .3rem .2rem;
        }

        .email-marked{
            color: green;
        }
    }
</style>