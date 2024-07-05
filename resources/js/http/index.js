import axios from 'axios';

export default {
    install: (app) => {
      app.config.globalProperties.$http = {
        showMesasge(msg, type = 'error') {
            // if (messageInstance) {
            //     //如果有已经打开的弹框就先关闭
            //     // messageInstance.close();
            //     // messageInstance = null;
            // } else {
            //     messageInstance = ElementUI.Message({
            //         'showClose': true,
            //         'message': msg || i18n.$t('请求出错，请重试'),
            //         'type': type,
            //         'duration': 3000,
            //         'onClose': () => {
            //             messageInstance = null;
            //         }
    
            //     });
            // }
        },
        // post
        post(url, data, isLoading = false) {
            let requestUrl = route(url);

            return new Promise((resolve, reject) => {
                let req = {};
                let loading = {
                    'configs': {
                        'isLoading': isLoading
                    }
                };
                axios
                    .post(requestUrl, req, loading)
                    .then((response) => {
                        if (response) {
                            resolve(response.data);
                        }
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        // get
        get(url, params, isLoading = false) {
            let paramsStr = '';
            if (params) {
                paramsStr = params;
            }
            let requestUrl = url + paramsStr;

            let configs = {
                'isLoading': isLoading
            };
    
            return axios
                    .get(requestUrl, {
                        'configs': configs
                    });
        },
        // patch
        patch(url, data) {
            let requestUrl = url;

            return new Promise((resolve, reject) => {
                let req = {};
                let configs = {
                    'isLoading': isLoading
                };
                axios
                    .patch(requestUrl, req, configs)
                    .then((response) => {
                        resolve(response.data);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        //put
        put(url, data, isLoading = false) {
            let requestUrl = url;
            let req = data || {};
            return axios
                    .put(requestUrl, req, isLoading);
        },
        //putUrl  将请求参数拼接在URL中
        putUrl(url, data, isLoading = false) {
            let requestUrl = url;

            let req = data || {};
            let configs = {
                'isLoading': isLoading
            };
            return axios
                    .put(requestUrl, null, {
                        'params': req,
                        configs
                    });
        },
        //delete
        delete(url, params, isLoading = false) {
            let paramsStr = '';
            if (params) {
                paramsStr = params;
            }
            let requestUrl = url + paramsStr;
            let configs = {
                'isLoading': isLoading
            };
            return axios
                    .delete(requestUrl, {
                        'configs': configs
                    });
        },
        //deleteArray
        deleteArray(url, params, isLoading = false) {
            let requestUrl = url;
            let req = data || {};
            let configs = {
                'isLoading': isLoading
            };
            return axios
                    .delete(requestUrl, {
                        'data': req,
                        configs
                    });
        },
        pnPost(url, data, isLoading = false, cb) {
            let requestUrl = url;
            let req = {};
            let loading = {
                'configs': {
                    'isLoading': isLoading
                }
            };
            axios
                .post(requestUrl, req, loading)
                .then((res) => {
                    if (res) {
                        cb(res);
                    }
                })
                .catch((err) => {
                    reject(err);
                });
        }
      }
    }
  }

