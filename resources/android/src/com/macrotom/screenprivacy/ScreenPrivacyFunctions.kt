package com.macrotom.screenprivacy

import android.app.Activity
import android.os.Build
import android.util.Log
import android.view.WindowManager
import androidx.fragment.app.FragmentActivity
import com.nativephp.mobile.bridge.BridgeFunction
import com.nativephp.mobile.bridge.BridgeResponse
import com.nativephp.mobile.utils.NativeActionCoordinator

object ScreenPrivacyFunctions {
    private const val TAG = "ScreenPrivacy"

    class Init(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            Log.d(TAG, "!!! INIT Screen Privacy Called !!!")
            activity.runOnUiThread {
                Log.d(TAG, "!!! INIT running on UI thread !!!")
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.UPSIDE_DOWN_CAKE) {
                    try {
                        Log.d(TAG, "!!! Registering ScreenCaptureCallback (Android 14+) !!!")
                        val callback = Activity.ScreenCaptureCallback {
                            Log.d(TAG, "!!! SCREENSHOT DETECTED VIA CALLBACK !!!")
                            NativeActionCoordinator.dispatchEvent(
                                activity,
                                "MacroTom\\ScreenPrivacy\\Events\\ScreenCaptureDetected",
                                "{}"
                            )
                        }
                        activity.registerScreenCaptureCallback(activity.mainExecutor, callback)
                        Log.d(TAG, "!!! Callback registered successfully !!!")
                    } catch (e: Exception) {
                        Log.e(TAG, "!!! Error registering callback: ${e.message} !!!", e)
                    }
                } else {
                    Log.d(TAG, "!!! Android version < 14, skipping callback registration !!!")
                }
            }
            return BridgeResponse.success()
        }
    }

    class Enable(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            Log.d(TAG, "!!! ENABLE Screen Privacy Protection Called !!!")
            activity.runOnUiThread {
                try {
                    val window = activity.window
                    if (window != null) {
                        window.addFlags(WindowManager.LayoutParams.FLAG_SECURE)
                        Log.d(TAG, "!!! FLAG_SECURE added to window: ${window} !!!")
                        NativeActionCoordinator.dispatchEvent(
                            activity,
                            "MacroTom\\ScreenPrivacy\\Events\\ScreenPrivacyEnabled",
                            "{}"
                        )
                    } else {
                        Log.e(TAG, "!!! activity.window is NULL !!!")
                    }
                } catch (e: Exception) {
                    Log.e(TAG, "!!! Error in Enable: ${e.message} !!!", e)
                }
            }
            return BridgeResponse.success()
        }
    }

    class Disable(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            Log.d(TAG, "!!! DISABLE Screen Privacy Protection Called !!!")
            activity.runOnUiThread {
                try {
                    val window = activity.window
                    if (window != null) {
                        window.clearFlags(WindowManager.LayoutParams.FLAG_SECURE)
                        Log.d(TAG, "!!! FLAG_SECURE cleared from window: ${window} !!!")
                        NativeActionCoordinator.dispatchEvent(
                            activity,
                            "MacroTom\\ScreenPrivacy\\Events\\ScreenPrivacyDisabled",
                            "{}"
                        )
                    } else {
                        Log.e(TAG, "!!! activity.window is NULL !!!")
                    }
                } catch (e: Exception) {
                    Log.e(TAG, "!!! Error in Disable: ${e.message} !!!", e)
                }
            }
            return BridgeResponse.success()
        }
    }

    class IsEnabled(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            val window = activity.window
            val flags = window?.attributes?.flags ?: 0
            val enabled = (flags and WindowManager.LayoutParams.FLAG_SECURE) != 0
            Log.d(TAG, "!!! Check IsEnabled: $enabled (Flags: $flags) (Window: $window) !!!")
            return BridgeResponse.success(mapOf("enabled" to enabled))
        }
    }

    class IsRecording(private val activity: FragmentActivity) : BridgeFunction {
        override fun execute(parameters: Map<String, Any>): Map<String, Any> {
            Log.d(TAG, "!!! IsRecording Called (Always returns false for now) !!!")
            return BridgeResponse.success(mapOf("isRecording" to false))
        }
    }
}
