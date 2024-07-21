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
                                <div class="flex">
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
                                </div>
                                <div class="flex" style="margin-left: auto;">
                                    <div class="my-auto">
                                        <va-button
                                            icon="table_view"
                                            preset="secondary"
                                            icon-color="#11111180"
                                            class="mr-3"
                                            pressed-behavior="mask" :pressed-opacity="1" pressed-mask-color="warning"
                                            style="width: 36px; height: 36px;"
                                            @click="showTblConfigModal = true"
                                        >
                                        </va-button>
                                    </div>
                                    <div class="my-auto">
                                        <va-button
                                            icon="repartition"
                                            preset="secondary"
                                            icon-color="#11111180"
                                            pressed-behavior="mask" :pressed-opacity="1" pressed-mask-color="warning"
                                            style="width: 36px; height: 36px;"
                                            @click="showDbConfigModal = true"
                                        >
                                        </va-button>
                                    </div>
                                </div>
                            </div>
                        </va-form>
                    </div>
                </div>
                <div class="flex md12 align-start">
                    <div>
                        <va-divider />
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
                                    <th colspan="1"><span>TOP {{ awardachievers_count }}</span> / <span>CONDUCT</span></th>
                                    <th colspan="1"></th>
                                </tr>
                            </template>

                            <template #header(award)="{ label }">
                                <va-chip square icon="auto_awesome" color="info" size="small">{{ label }}</va-chip>
                            </template>
                            <template #cell(award)="{ value }">
                                <div class="flex">
                                    <div class="my-auto" style="flex: none;">
                                        <!-- achievers -->
                                        <va-button
                                            id="awardbtn"
                                            preset="secondary" border-color="primary"
                                            size="small"
                                            class="achievers"
                                            style="height: 10px;"
                                            @click="show_awardee(value.split(',', 3).pop().toString(), value.split(',', 1).pop().toString())"
                                            :disabled="value.split(',', 1).pop().toString() > awardachievers_count"
                                        >
                                            {{ value.split(',', 1).pop().toString() <= awardachievers_count ? "TOP " + value.split(',', 1).pop().toString() : 'N/ A' }}
                                        </va-button>
                                    </div>
                                    <div class="my-auto" style="flex: none;">
                                        <!-- good conduct -->
                                        <va-button
                                            id="awardbtn"
                                            :icon="value.split(',', 2).pop().toString() == 'true' ? 'thumb_up' : ''"
                                            color="secondary"
                                            preset="secondary" border-color="secondary"
                                            size="small"
                                            class="conduct ml-2"
                                            style="height: 10px;"
                                            @click="show_awardee(value.split(',', 3).pop().toString(), value.split(',', 2).pop().toString())"
                                            :disabled="value.split(',', 2).pop().toString() != 'true'"
                                        >
                                            {{ value.split(',', 2).pop().toString() == 'true' ? 'CONDUCT' : 'N/ A' }}
                                        </va-button>
                                    </div>
                                </div>
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

    <va-modal v-model="showUploadModal" :message="grade_fileup_message" :cancel-text=null title="Notice" />
    <va-modal
        v-model="showTblConfigModal"
        id="configtblmodal"
        no-padding
        @click-outside="gradetblcfg_cancel()"
    >
        <template #content>
            <va-card-title>
                Table Configuration
            </va-card-title>
            <va-card-content>
                <div class="row">
                    <div class="flex md12 mt-3 mb-2">
                        <va-input
                            class="mr-2"
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
                    <div class="flex md12 mt-4 mb-2">
                        <va-input
                            type="number"
                            :placeholder="awardachievers_count.toString()"
                            label="Top achievers"
                            v-model.number="gradetblcfg_achievers"
                            min="1"
                            @blur="validatetbl_awardachievers()"
                        />
                    </div>
                    <div class="flex md12 mb-2">
                        <va-input
                            class="mr-2"
                            type="number"
                            :placeholder="grade_minpassgrd.toString()"
                            label="Min. passing grade"
                            v-model.number="gradetblcfg_minpassgrd"
                            min="70"
                            max="100"
                            @blur="validatetbl_maxminpassgrd()"
                        />
                        <va-input
                            type="number"
                            :placeholder="grade_warnoffset.toString()"
                            label="Warn offset from min. passing grade.."
                            v-model.number="gradetblcfg_warnoffset"
                            min="1"
                            max="10"
                            @blur="validatetbl_maxwarnoffset()"
                        />
                    </div>
                    <div class="flex md12 mb-2">
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
                <va-button @click="gradetblcfg_cancel()" color="rgb(118, 124, 136)" preset="secondary">Cancel</va-button>
                <va-button @click="gradetblcfg_save()" :disabled="!gradetblcfg_modified">Save</va-button>
            </va-card-actions>
        </template>
    </va-modal>
    <va-modal
        v-model="showDbConfigModal"
        id="configdbmodal"
        no-padding
        @click-outside="gradedbcfg_cancel()"
    >
        <template #content>
            <va-card-title>
                Database Configuration
            </va-card-title>
            <va-card-content>
                <div class="row">
                    <div class="flex md12 mt-3 mb-2">
                        <va-switch
                            v-model="gradedbdata_truncate"
                            label="Clear Data"
                            size="small"
                        />
                        <va-switch
                            v-model="gradedbdata_seed"
                            label="Re-initialize Data"
                            size="small"
                            :disabled="!gradedbdata_truncate"
                        />
                    </div>
                </div>
                <va-divider orientation="left">
                    <span class="px-3">Data Conditioning</span>
                </va-divider>
            </va-card-content>
            <va-card-actions>
                <div class="flex">
                    <va-checkbox
                        v-if="gradedbdata_truncate"
                        v-model="gradedbdata_agree"
                        :label="'On commit, this action cannot be undone'"
                        error-messages="Please agree to the terms"
                        :error="!gradedbdata_agree"
                    />
                </div>
                <div class="flex" style="margin-left: auto;">
                    <va-button @click="gradedbcfg_cancel()" color="rgb(118, 124, 136)" preset="secondary">Cancel</va-button>
                    <va-button @click="gradedbcfg_commit()" :disabled="!gradedbdata_agree || !gradedbdata_truncate">Commit</va-button>
                </div>
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
            <!-- <va-image v-if="awardee !== ''" :ratio="16/9" src="img/cert_acadex.jpg" /> -->
            <va-image :ratio="16/9" :src="awardtype == 'true' ? 'img/cert_gcondt.jpg' : 'img/cert_acadex.jpg'" />
            <h2 class="va-h2 awardname">{{ awardee }}</h2>
            <va-card-actions style="justify-content: flex-end; padding: 16px;">
                <va-button @click="ok" size="small" color="warning" style="padding: 2px 20px;">Close</va-button>
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
    top: 34%;
    left: 0;
    right: 0;
}
</style>

<script>
import Navbar from '@/components/Navbar'
import ConfettiExplosion from "vue-confetti-explosion"

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
            grade_minpassgrd: 80,
            grade_warnoffset: 5,
            grade_minconduct: 85,
            showUploadModal: false,
            grade_fileup_message: null,
            showTblConfigModal: false,
            gradetblcfg_perpage: null,
            gradetblcfg_currpage: null,
            gradetblcfg_minpassgrd: null,
            gradetblcfg_warnoffset: null,
            gradetblcfg_minconduct: null,
            gradetblcfg_achievers: null,
            gradetblcfg_modified: false,
            showAwardsModal: false,
            awardee: null,
            awardtype: null,
            awardachievers_count: 10,
            showDbConfigModal: false,
            gradedbdata_message: null,
            gradedbdata_indicator: null,
            gradedbdata_truncate: false,
            gradedbdata_seed: false,
            gradedbdata_agree: false
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
        gradedbcfg_commit() {
            axios({
                method: 'POST',
                type: 'JSON',
                url: '/condition_grades_db',
                data: { agreed: this.gradedbdata_agree, truncate: this.gradedbdata_truncate, seed: this.gradedbdata_seed }
            }).then(response => {
                this.gradedbdata_message = response.data.message;
                if (response.data.status == 1) this.gradedbdata_indicator = 'warning';
                else this.gradedbdata_indicator = 'danger';

                this.$vaToast.init({message: this.gradedbdata_message, color: this.gradedbdata_indicator});

                this.view_grade();
            }).catch(err => {
                this.gradedbdata_message = 'Something went wrong'
            });

            this.gradedbdata_truncate = null;
            this.gradedbdata_seed = null;
            this.gradedbdata_agree = null;

            this.showDbConfigModal = false;
        },
        gradedbcfg_cancel() {
            this.gradedbdata_truncate = null;
            this.gradedbdata_seed = null;
            this.gradedbdata_agree = null;

            this.showDbConfigModal = false;
        },
        gradetblcfg_save() {
            this.showTblConfigModal = false;

            if (this.gradetblcfg_perpage != null
                && this.gradetblcfg_perpage != this.grade_perpage
                && this.gradetblcfg_perpage != 0)
                this.grade_perpage = this.gradetblcfg_perpage;

            this.gradetblcfg_perpage = null;

            if (this.gradetblcfg_currpage != null
                && this.gradetblcfg_currpage != this.grade_currpage
                && this.gradetblcfg_currpage != 0)
                this.grade_currpage = this.gradetblcfg_currpage;

            this.gradetblcfg_currpage = null;

            if (this.gradetblcfg_minpassgrd != null
                && this.gradetblcfg_minpassgrd != this.grade_minpassgrd
                && this.gradetblcfg_minpassgrd != 0)
                this.grade_minpassgrd = this.gradetblcfg_minpassgrd;

            this.gradetblcfg_minpassgrd = null;

            if (this.gradetblcfg_warnoffset != null
                && this.gradetblcfg_warnoffset != this.grade_warnoffset
                && this.gradetblcfg_warnoffset != 0)
                this.grade_warnoffset = this.gradetblcfg_warnoffset;

            this.gradetblcfg_warnoffset = null;

            if (this.gradetblcfg_minconduct != null
                && this.gradetblcfg_minconduct != this.grade_minconduct
                && this.gradetblcfg_minconduct != 0)
                this.grade_minconduct = this.gradetblcfg_minconduct;

            this.gradetblcfg_minconduct = null;

            if (this.gradetblcfg_achievers != null
                && this.gradetblcfg_achievers != this.awardachievers_count
                && this.gradetblcfg_achievers != 0)
                this.awardachievers_count = this.gradetblcfg_achievers;

            this.gradetblcfg_achievers = null;

            this.gradetblcfg_modified = false;
            this.view_grade();
        },
        gradetblcfg_cancel() {
            this.gradetblcfg_perpage = null;
            this.gradetblcfg_currpage = null;
            this.gradetblcfg_minpassgrd = null;
            this.gradetblcfg_warnoffset = null;
            this.gradetblcfg_minconduct = null;
            this.gradetblcfg_achievers = null;

            this.gradetblcfg_modified = false;
            this.showTblConfigModal = false;
        },
        validatetbl_maxperpage() {
            this.gradetblcfg_modified = true;

            if (this.gradetblcfg_perpage > this.grade_perpage)
                this.gradetblcfg_perpage = this.studs.length;
        },
        validatetbl_maxcurrpage() {
            this.gradetblcfg_modified = true;

            if (this.gradetblcfg_currpage > this.grade_currpage)
                this.gradetblcfg_currpage = (this.grade_perpage && this.grade_perpage !== 0)
                    ? Math.ceil(this.studs.length / this.grade_perpage)
                    : this.studs.length;
        },
        validatetbl_maxminpassgrd() {
            this.gradetblcfg_modified = true;

            if (this.gradetblcfg_minpassgrd < 70)
                this.gradetblcfg_minpassgrd = (this.grade_minpassgrd > this.gradetblcfg_minpassgrd) ? this.gradetblcfg_minpassgrd : 70;

            if (this.gradetblcfg_minpassgrd > 100) this.gradetblcfg_minpassgrd = 100;
        },
        validatetbl_maxwarnoffset() {
            this.gradetblcfg_modified = true;

            let setwarn_offset = 0;

            if (this.gradetblcfg_minpassgrd == null)
                setwarn_offset = this.grade_minpassgrd - this.gradetblcfg_warnoffset;
            else
                setwarn_offset = this.gradetblcfg_minpassgrd - this.gradetblcfg_warnoffset;

            if (setwarn_offset < 70) {
                let subwarn_offset = this.gradetblcfg_minpassgrd - this.gradetblcfg_warnoffset;
                let newwarn_offset = ((this.gradetblcfg_minpassgrd == null) ? this.grade_minpassgrd : this.gradetblcfg_minpassgrd) - (setwarn_offset + (70 - subwarn_offset));

                this.gradetblcfg_warnoffset = newwarn_offset;
            }
        },
        validatetbl_maxminconduct() {
            this.gradetblcfg_modified = true;

            if (this.gradetblcfg_minconduct < 70)
                this.gradetblcfg_minconduct = (this.grade_minconduct > this.gradetblcfg_minconduct) ? this.gradetblcfg_minconduct : 70;

            if (this.gradetblcfg_minconduct > 100) this.gradetblcfg_minconduct = 100;
        },
        validatetbl_awardachievers() {
            this.gradetblcfg_modified = true;
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

                    // achievers
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
                    if (stud['grade'] >= this.grade_minpassgrd) stud['status'] = 1;
                    else if (stud['grade'] >= this.grade_minpassgrd - this.grade_warnoffset) stud['status'] = 2;
                    else if (stud['grade'] < this.grade_minpassgrd - this.grade_warnoffset) stud['status'] = 3;
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
                this.grade_fileup_message = response.data.message;
                this.showUploadModal = true;

                this.view_grade();
            }).catch(err => {
                this.grade_fileup_message = 'Something went wrong'
                this.showUploadModal = true;
            });
        },
        show_awardee(name, value) {
            this.showAwardsModal = true;

            this.awardee = name;
            this.awardtype = value;
        }
    }
})
</script>
