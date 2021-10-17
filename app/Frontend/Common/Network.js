import axios from 'axios';
import * as FileSaver from './FileSaver'

// axios.interceptors.response.use(response => {
//     return response;
//  }, error => {
//     if (error.response.status === 401) {
//         location.href = '/login'
//     }
//     return error;
// });

export default {
    methods: {
        onLogout() {
            axios('logout').then(() => {
                this.$store.commit('logoutUser');
                this.$router.push('/login');
            });
        },
        onError(errorInfo) {
            if(!errorInfo) {
                this.$store.commit('clearError');
                return;
            }

            let message = '';
            if (errorInfo.error) {
                message = errorInfo.error;
            } else {
                message = JSON.stringify(errorInfo);
            }

            this.$store.commit('setError', message);
        },
        request(requestData) {
            this.$store.commit('setIsLoading',true);
            
            return axios(requestData).then(response => {
                this.$store.commit('setIsLoading',false);
                return Promise.resolve(response.data);
            }).catch(error => {
                this.$store.commit('setIsLoading',false);
                this.onError(error.response.data);
                return Promise.reject(error.response.data);
            });
        },
        downloadFileStream: function(url, fileName, fileType) {
            fileType = fileType || 'pdf';
            let that = this;
            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.responseType = 'arraybuffer';
            that.$store.commit('setIsLoading',true);

            let promise = new Promise(function(resolve, reject) {
                xhr.addEventListener('load', function() {
                    that.$store.commit('setIsLoading',false);
                    if (xhr.status === 200) {
                        let blob = new Blob([xhr.response], { type: "application/pdf" });
                        FileSaver.saveAs(blob, fileName + '.' + fileType);

                        resolve();
                    } else {
                        reject();
                    }
                });
            });

            xhr.send();

            return promise;
        },
        onUploadFile: function(formData,afterFileUploaded) {
            formData = formData || new FormData(this.$refs.form);
            afterFileUploaded = afterFileUploaded || this.onFileUploaded;            

            this.$store.commit('setIsLoading',true);
            return axios.post(this.uploadUrl,formData).then(response => {
                this.$store.commit('setIsLoading',false);
                afterFileUploaded();
                return Promise.resolve(response.data);
            }).catch(error => {
                this.$store.commit('setIsLoading',false);
                this.onError(error.response.data);
                return Promise.reject(error.response.data);
            });
        },
        onFileUploaded: function() {},
        objectToQueryParams(obj) {
            let str = [];
            for (let p in obj) {
                if (obj.hasOwnProperty(p)) {
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                }
            }
            return str.join("&");
        }
    }
}