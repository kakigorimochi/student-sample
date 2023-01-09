<template>
    <Navbar />

    <div class="row align-center" style="height: 80vh; margin: 3em;">
        <div class="flex" style="width: 100%;">
            <div class="row">
                <div class="flex md12">
                    <div>
                        <va-form>
                            <div class="row" style="text-align: center;">
                                <div>
                                    <va-file-upload
                                        v-model="grade_file"
                                        hide-file-list
                                        type="single"
                                        file-types="xlsx"
                                        upload-button-text="Select file"
                                    />
                                </div>
                                <div class="ml-5 my-auto align-center" style="margin-right: 105px;">
                                    {{ grade_file ? grade_file.name : "No file selected..." }}
                                </div>
                                <div class="my-auto">
                                    <va-button
                                        color="info"
                                        gradient icon-right="upload_file"
                                        icon-color="#ffffff50"
                                        style="height: 36px; margin: 0!important;"
                                        @click="import_grade()">
                                        Upload
                                    </va-button>
                                </div>
                            </div>
                        </va-form>
                    </div>
                </div>
                <div class="flex md12">
                    <div>
                        <va-data-table
                            :items="studs"
                            :columns="grade_columns"
                            :per-page="grade_perpage"
                            :current-page="grade_currpage"
                            animated
                            hoverable
                        >
                            <template #header(award)="{ label }">
                                <va-chip square icon="auto_awesome" color="info" size="small">{{ label }}</va-chip>
                            </template>
                            <template #cell(award)="{ value }">
                                <va-button
                                    v-if="value <= 10"
                                    id="awardbtn"
                                    preset="secondary" border-color="primary"
                                    size="small"
                                    style="height: 10px; margin: 0!important;"
                                    @click="show_awards(value)"
                                >
                                TOP &nbsp;#{{ value }}
                                </va-button>
                                <va-button
                                    v-if="value > 10"
                                    id="awardbtn"
                                    preset="secondary" border-color="primary"
                                    size="small"
                                    style="height: 10px; margin: 0!important;"
                                    disabled
                                >
                                N/A
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
        v-model="showAwardsModal"
        id="awardsmodal"
        :message="message"
        no-padding
        :cancel-text=null
    >
    <template #content="{ ok }">
        <ConfettiExplosion />
        <va-image :ratio="16/9" src="img/certificate.png" />
        <h2 class="va-h2 awardname">{{ awardeeName }}</h2>
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
            showUploadModal: false,
            message: null,
            showAwardsModal: false,
            awardeeName: null
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
        view_grade() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/view_grades',
            }).then(response => {
                let studs = response.data;

                // award
                for (let i = 0; i < studs.length; i += 1) {
                    var award = i + 1;
                    studs[i]['award'] = award;
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
        show_awards(value) {
            this.showAwardsModal = true;
            this.awardeeName = this.studs[value - 1].name;
        }
    }
})
</script>
