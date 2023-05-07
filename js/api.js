class RahMahdAPI {
    static API_BASE_URL = "http://localhost/faezeh1127/api";

    static async managerLogin(user, password) {
        const URL_ENDPOINT = '/manager-login';

        let url = this.API_BASE_URL + URL_ENDPOINT;

        let send_json = {
            username: user,
            password: password
        };
        
        return await axios.post(url, send_json);
    }
}
