/**
 * Screen Privacy Plugin for NativePHP Mobile
 */

const baseUrl = '/_native/api/call';

/**
 * Internal bridge call function
 * @private
 */
async function bridgeCall(method, params = {}) {
    const response = await fetch(baseUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ method, params })
    });

    const result = await response.json();

    if (result.status === 'error') {
        throw new Error(result.message || 'Native call failed');
    }

    const nativeResponse = result.data;
    if (nativeResponse && nativeResponse.data !== undefined) {
        return nativeResponse.data;
    }

    return nativeResponse;
}

function init() {
    return bridgeCall('ScreenPrivacy.Init');
}

function enable() {
    return bridgeCall('ScreenPrivacy.Enable');
}

function disable() {
    return bridgeCall('ScreenPrivacy.Disable');
}

function isEnabled() {
    return bridgeCall('ScreenPrivacy.IsEnabled');
}

function isRecording() {
    return bridgeCall('ScreenPrivacy.IsRecording');
}

export const screenPrivacy = {
    init,
    enable,
    disable,
    isEnabled,
    isRecording,
};

export default screenPrivacy;
