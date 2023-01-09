<template>
    <Navbar />

    <div class="row" style="height: 80vh; margin: 3em;">
        <div class="flex" style="width: 100%;">
            <div class="row align-start" style="display: block;">
                <div class="flex md12">
                    <div>
                    <!-- <div class="item"> -->
                        <va-form>
                            <div class="row" style="text-align: center;">
                                <div>
                                    <va-file-upload
                                        v-model="grade_file"
                                        type="single"
                                        file-types="xlsx"
                                        upload-button-text="Select file"
                                        hide-file-list
                                    />
                                </div>
                                <div class="ml-5 my-auto align-center" style="margin-right: 105px;">
                                    {{ grade_file ? grade_file.name : "No file selected..." }}
                                </div>
                                <div class="my-auto">
                                    <va-button
                                        color="info"
                                        gradient
                                        icon-right="upload_file"
                                        icon-color="#ffffff50"
                                        class="mr-3"
                                        style="height: 36px;"
                                        @click="import_grade()"
                                    >
                                        Upload
                                    </va-button>
                                </div>
                                <div class="my-auto">
                                    <va-button
                                        icon="settings"
                                        preset="primary"
                                        icon-color="#11111150"
                                        style="width: 36px; height: 36px;"
                                        @click="showTblConfigModal = true"
                                    >
                                    </va-button>
                                </div>
                            </div>
                        </va-form>
                    </div>
                </div>
                <div class="flex md12 align-start">
                    <div>
                    <!-- <div class="item"> -->
                        <va-data-table
                            id="gradetbl"
                            :items="studs"
                            :columns="grade_columns"
                            :per-page="grade_perpage"
                            :current-page="grade_currpage"
                            no-data-html="No graded student(s) to show"
                            style="margin-bottom: 3em;"
                            animated
                        >
                            <template #headerAppend>
                                <tr class="table-slots__head">
                                    <th colspan="5"></th>
                                    <th colspan="1"><span>TOP 10</span> / <span>CONDUCT</span></th>
                                    <th colspan="1"></th>
                                </tr>
                            </template>

                            <template #header(award)="{ label }">
                                <va-chip square icon="auto_awesome" color="info" size="small">{{ label }}</va-chip>
                            </template>
                            <template #cell(award)="{ value }">
                                <!-- top 10 -->
                                <va-button
                                    id="awardbtn"
                                    preset="secondary" border-color="primary"
                                    size="small"
                                    class="top10"
                                    style="height: 10px;"
                                    @click="show_awardee(value.split(',', 3).pop().toString())"
                                    :disabled="value.split(',', 1).pop().toString() > 10"
                                >
                                    {{ value.split(',', 1).pop().toString() <= 10 ? "TOP " + value.split(',', 1).pop().toString() : 'N/ A' }}
                                </va-button>

                                <!-- good conduct -->
                                <va-button
                                    id="awardbtn"
                                    :icon="value.split(',', 2).pop().toString() == 'true' ? 'thumb_up' : ''"
                                    color="secondary"
                                    preset="secondary" border-color="secondary"
                                    size="small"
                                    class="conduct ml-2"
                                    style="height: 10px;"
                                    @click="show_awardee(value.split(',', 3).pop().toString())"
                                    :disabled="value.split(',', 2).pop().toString() != 'true'"
                                >
                                    {{ value.split(',', 2).pop().toString() == 'true' ? 'CONDUCT' : 'N/ A' }}
                                </va-button>
                            </template>

                            <template #cell(status)="{ value }">
                                <va-badge
                                    v-if="value == 0" text="n/a" color="secondary"
                                />
                                <va-badge
                                    v-else-if="value == 1" text="pass" color="success"
                                />
                                <va-badge
                                    v-else-if="value == 2" text="warn" color="warning"
                                />
                                <va-badge
                                    v-else-if="value == 3" text="fail" color="danger"
                                />
                            </template>

                            <template #bodyAppend>
                                <tr>
                                    <td colspan="8">
                                        <div class="table-example--pagination">
                                        <va-pagination
                                            v-model="grade_currpage"
                                            input
                                            :pages="grade_pages"
                                            class="mt-3"
                                        />
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </va-data-table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <va-modal v-model="showUploadModal" :message="message" :cancel-text=null title="Notice" />
    <va-modal
        v-model="showTblConfigModal"
        id="configtblmodal"
        no-padding
        @cancel="gradetblcfg_cancel()"
        @click-outside="gradetblcfg_cancel()"
    >
        <template #content="{ ok }">
            <va-card-title>
                Table Configuration
            </va-card-title>
            <va-card-content>
                <div class="row">
                    <div class="flex md12 mt-3 mb-2">
                        <va-input
                            class="mr-3"
                            type="number"
                            :placeholder="grade_perpage.toString()"
                            label="Students per page"
                            v-model.number="gradetblcfg_perpage"
                            min="1"
                            :max="studs.length.toString()"
                            @blur="validatetbl_maxperpage()"
                        />
                        <va-input
                            type="number"
                            :placeholder="grade_currpage.toString()"
                            label="Current page"
                            v-model.number="gradetblcfg_currpage"
                            min="1"
                            :max="grade_pages.toString()"
                            @blur="validatetbl_maxcurrpage()"
                        />
                    </div>
                </div>
                <va-divider orientation="left">
                    <span class="px-3">Items and Pagination</span>
                </va-divider>
                <div class="row">
                    <div class="flex md12 mt-5 mb-2">
                        <va-input
                            type="number"
                            :placeholder="grade_minconduct.toString()"
                            label="Min. good conduct"
                            v-model.number="gradetblcfg_minconduct"
                            min="70"
                            max="100"
                            @blur="validatetbl_maxminconduct()"
                        />
                    </div>
                </div>
                <va-divider orientation="left">
                    <span class="px-3">Certificate Awards</span>
                </va-divider>
            </va-card-content>
            <va-card-actions style="justify-content: flex-end;">
                <va-button @click="ok" color="rgb(118, 124, 136)" preset="secondary">Cancel</va-button>
                <va-button @click="gradetblcfg_save()">Save</va-button>
            </va-card-actions>
        </template>
    </va-modal>
    <va-modal
        v-model="showAwardsModal"
        id="awardsmodal"
        no-padding
        blur
    >
        <template #content="{ ok }">
            <ConfettiExplosion />
            <va-image :ratio="16/9" src="img/certificate.jpg" />
            <h2 class="va-h2 awardname">{{ awardee }}</h2>
            <va-card-actions style="justify-content: flex-end;">
                <va-button @click="ok" color="warning">Close</va-button>
            </va-card-actions>
        </template>
    </va-modal>
</template>

<style lang="scss" scoped>
.item {
    border: 1px solid var(--va-background-border);
    background-color: var(--va-background-primary);
    text-align: center;
}
.table-slots__head {
    color: var(--va-info);
    background-color: var(--va-background-primary);
    border-bottom: none;
}
.table-example--pagination {
    display: flex;
    justify-content: center;
}
.va-button__content{
    padding: 0;
}
.awardname {
    position: absolute;
    text-align: center;
    margin-left: auto;
    margin-right: auto;
    top: 32.5%;
    left: 0;
    right: 0;
}
</style>

<script>
import Navbar from '@/components/Navbar'
import ConfettiExplosion from "vue-confetti-explosion";

import { defineComponent } from 'vue'

export default defineComponent ({
    components: {
        Navbar, ConfettiExplosion
    },
    data () {
        const grade_columns = [
        { key: 'stud_no', label: 'Student No.' },
        { key: 'name' },
        { key: 'email' },
        { key: 'grade' },
        { key: 'conduct' },
        { key: 'award' },
        { key: 'status' }
        ]

        return {
            studs: [],
            grade_columns,
            grade_perpage: 15,
            grade_currpage: 1,
            grade_file: '',
            grade_minconduct: 85,grade_minconduct: 85,
            showUploadModal: false,
            message: null,
            showTblConfigModal: false,
            gradetblcfg_perpage: null,
            gradetblcfg_currpage: null,
            gradetblcfg_minconduct: null,
            showAwardsModal: false,
            awardee: null
        }
    },
    computed: {
        grade_pages() {
            return (this.grade_perpage && this.grade_perpage !== 0)
                ? Math.ceil(this.studs.length / this.grade_perpage)
                : this.studs.length
        },
    },
    mounted() {
        this.view_grade();
    },
    methods: {
        gradetblcfg_save() {
            this.showTblConfigModal = false;

            if (this.gradetblcfg_perpage != null
                && this.gradetblcfg_perpage != this.grade_perpage
                && this.gradetblcfg_perpage != 0)
                this.grade_perpage = this.gradetblcfg_perpage;

            if (this.gradetblcfg_currpage != null
                && this.gradetblcfg_currpage != this.grade_currpage
                && this.gradetblcfg_currpage != 0)
                this.grade_currpage = this.gradetblcfg_currpage;

            if (this.gradetblcfg_minconduct != null
                && this.gradetblcfg_minconduct != this.grade_minconduct
                && this.gradetblcfg_minconduct != 0)
                this.grade_minconduct = this.gradetblcfg_minconduct;

            this.gradetblcfg_perpage = null;
            this.gradetblcfg_currpage = null;
            this.gradetblcfg_minconduct = null;

            this.view_grade();
        },
        gradetblcfg_cancel() {
            this.gradetblcfg_perpage = null;
            this.gradetblcfg_currpage = null;
            this.gradetblcfg_minconduct = null;
        },
        validatetbl_maxperpage() {
            if (this.gradetblcfg_perpage > this.grade_perpage)
                this.gradetblcfg_perpage = this.studs.length;
        },
        validatetbl_maxcurrpage() {
            if (this.gradetblcfg_currpage > this.grade_currpage)
                this.gradetblcfg_currpage = (this.grade_perpage && this.grade_perpage !== 0)
                    ? Math.ceil(this.studs.length / this.grade_perpage)
                    : this.studs.length;
        },
        validatetbl_maxminconduct() {
            if (this.gradetblcfg_minconduct < 70) this.gradetblcfg_minconduct = 70;
            if (this.gradetblcfg_minconduct > 100) this.gradetblcfg_minconduct = 100;
        },
        view_grade() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/view_grades',
            }).then(response => {
                let studs = response.data;

                // award
                for (let i = 0; i < studs.length; i += 1) {
                    let top_awardee = [];
                    let conduct_awardee = [];
                    let awardee = [];

                    // top 10
                    var top_awards = i + 1;
                    top_awardee = top_awards;

                    // good conduct
                    if (studs[i]['conduct'] >= this.grade_minconduct) conduct_awardee = true;

                    // who
                    awardee = studs[i]['name'];

                    studs[i]['award'] = [top_awardee, conduct_awardee, awardee];
                }

                // status
                for (const stud of studs) {
                    if (stud['grade'] > 80) stud['status'] = 1;
                    else if (stud['grade'] >= 75) stud['status'] = 2;
                    else if (stud['grade'] < 75) stud['status'] = 3;
                    else stud['status'] = 0;
                }

                this.studs = studs;
            })
        },
        import_grade() {
            let form = new FormData();
            form.append('file', this.grade_file);

            axios({
                headers: {'Content-Type': 'multipart/form-data'},
                method: 'POST',
                type: 'JSON',
                url: '/import_grades',
                data: form
            }).then(response => {
                this.message = response.data.message;
                this.showUploadModal = true;

                this.view_grade();
            }).catch(err => {
                this.message = 'Something went wrong'
                this.showUploadModal = true;
            });
        },
        show_awardee(value) {
            this.showAwardsModal = true;
            this.awardee = value;
        }
    }
})
</script>
