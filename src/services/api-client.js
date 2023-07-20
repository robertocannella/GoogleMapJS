import axios, {CanceledError, CreateAxiosDefaults} from 'axios';

export default axios.create({
    baseURL: globalSiteData.siteUrl,
    headers: {
        'content-type': 'application/json',
        'X-WP-Nonce': globalSiteData.nonceX
}
});

export { CanceledError };