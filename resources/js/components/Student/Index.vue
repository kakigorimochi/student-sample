<template>
    <va-navbar color="primary" class="mb-3">
        <template #left>
            <va-navbar-item class="logo">Student Grades Sample</va-navbar-item>
        </template>
        <template #right>
            <va-navbar-item>Dashboard</va-navbar-item>
            <va-navbar-item>Students</va-navbar-item>
            <va-navbar-item>Subjects</va-navbar-item>
            <va-navbar-item>
                <va-icon name="search" color="textInverted" />
            </va-navbar-item>
            <va-navbar-item>
                <va-icon name="account_circle" color="textInverted" />
            </va-navbar-item>
        </template>
    </va-navbar>

    <div style="margin: 2.5em 10em;">
        <div class="row justify-center">
            <div class="flex" style="width: 100%;">
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
                        <div class="mx-5 my-auto align-center">
                            {{ grade_file ? grade_file.name : "No file selected..." }}
                        </div>
                    </div>
                </va-form>
                <div class="my-auto">
                    <va-button
                        color="info"
                        gradient icon-right="upload_file"
                        icon-color="#ffffff50"
                        class="mr-3 mb-2"
                        @click="import_grade()">
                        Upload
                    </va-button>
                </div>
            </div>
        </div>
        <div class="row align-content-center">
            <div class="flex" style="width: 100%;">
                <div class="va-table-responsive my-5">
                    <table class="va-table">
                        <thead>
                            <tr>
                                <th>Student No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="stud in studs" :key="stud.id">
                                <td>{{stud.stud_no}}</td>
                                <td>{{stud.name}}</td>
                                <td>{{stud.email}}</td>
                                <td>{{stud.grade}}</td>
                                <td>
                                    <va-badge
                                        v-if="stud.status == 0" text="Pass" color="success"
                                    />
                                    <va-badge
                                        v-else-if="stud.status == 1" text="Warn" color="warning"
                                    />
                                    <va-badge
                                        v-else-if="stud.status == 2" text="Fail" color="danger"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <va-modal
        v-model="showUploadModal"
        :message="message"
        :cancel-text=null
        title="Notice" />
</template>

<style scoped>
.logo {
  font-weight: 600;
  font-size: 1.5rem;
}
</style>

<script>
export default {
    data () {
        return {
            studs: [],
            grade_file: '',
            showUploadModal: false,
            message: null
        }
    },
    mounted() {
        this.view_grade();
    },
    methods: {
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
        view_grade() {
            axios({
                method: 'GET',
                type: 'JSON',
                url: '/view_grades',
            }).then(response => {
                this.studs = response.data;
            })
        }
    }
}
</script>
