import axios from 'axios';
export const getLeadsData = (searchedKeyword = "", filterValues = "", pipelineId = 0, leadDisplayMode = 1) => {
    // Construct the URL with optional search parameters
    const baseUrl = route("dashboards.leads.get", pipelineId);
    let url = baseUrl;
    if (leadDisplayMode.value == 2) url = `${baseUrl}?view_type='table'`;
    if (url == baseUrl)
        url =
            url +
            `${searchedKeyword ? `?search=${searchedKeyword}` : ""}${filterValues ? `${searchedKeyword ? "&" : "?"}${filterValues}` : ""
            }`;
    else
        url =
            url +
            `${searchedKeyword ? `&search=${searchedKeyword}` : ""}${filterValues ? `${searchedKeyword ? "&" : "&"}${filterValues}` : ""
            }`;

    return new Promise((resolve, reject) => {
        axios
            .get(url)
            .then((response) => {
                // resolve(response);
                let leads_data = [];
                if (leadDisplayMode == 1) {
                    if (response.status == 200) {
                        leads_data = response.data;
                    } else {
                        leads_data = {
                            blocks: [],
                            stage_names: [],
                            stages: [],
                        };
                    }
                } else if (leadDisplayMode == 2) {
                    if (response.status == 200) {
                        leads_data = response.data.blocks;
                    } else {
                        leads_data = [];
                    }
                }
                resolve(leads_data);
            })
            .catch((error) => {
                reject(error);
            });
    })

};

