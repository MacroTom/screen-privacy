import Foundation
import UIKit

enum ScreenPrivacyFunctions {

    static var isPrivacyEnabled: Bool = false
    static var secureContainer: UIView?
    private static var isInitialized: Bool = false
    private static let secureTextField = UITextField()

    static func setup() {
        guard !isInitialized else { 
            print("🛡️ ScreenPrivacy: Already initialized, skipping setup")
            return 
        }
        isInitialized = true
        print("🛡️ ScreenPrivacy: Initializing native plugin for the first time")
        
        // Screenshot Detection
        NotificationCenter.default.addObserver(
            forName: UIApplication.userDidTakeScreenshotNotification,
            object: nil,
            queue: .main
        ) { _ in
            print("📸 ScreenPrivacy: Screenshot detected!")
            dispatchEvent("MacroTom\\ScreenPrivacy\\Events\\ScreenCaptureDetected", [:])
        }

        // Recording Detection
        NotificationCenter.default.addObserver(
            forName: UIScreen.capturedDidChangeNotification,
            object: nil,
            queue: .main
        ) { _ in
            let isCaptured = UIScreen.main.isCaptured
            print("⏺️ ScreenPrivacy: Screen recording state changed: \(isCaptured)")
            dispatchEvent("MacroTom\\ScreenPrivacy\\Events\\ScreenRecordingDetected", ["isRecording": isCaptured])
            
            if isPrivacyEnabled {
                updateProtectionState()
            }
        }
        
        // Initial setup of secure text field
        secureTextField.isSecureTextEntry = true
    }

    static func dispatchEvent(_ name: String, _ payload: [String: Any]) {
        print("📡 ScreenPrivacy: Dispatching event '\(name)' with payload: \(payload)")
        // Dispatch on main thread as required by NativePHP
        DispatchQueue.main.async {
            LaravelBridge.shared.send?(name, payload)
        }
    }

    static func updateProtectionState() {
        DispatchQueue.main.async {
            guard let window = UIApplication.shared.connectedScenes
                .compactMap({ $0 as? UIWindowScene })
                .flatMap({ $0.windows })
                .first(where: { $0.isKeyWindow }) else {
                print("⚠️ ScreenPrivacy: No key window found to apply protection")
                return
            }

            if isPrivacyEnabled {
                applySecureProtection(to: window)
            } else {
                removeSecureProtection(from: window)
            }
        }
    }

    static func applySecureProtection(to window: UIWindow) {
        print("🔒 ScreenPrivacy: Applying secure protection to window")
        
        // The "Secure View" trick:
        // We find the internal "Canvas" or "Layer" view of a secure UITextField.
        // Anything placed inside this view is automatically hidden from screenshots/recordings by iOS.
        
        if secureContainer == nil {
            // Force creation of internal secure subviews
            secureTextField.isSecureTextEntry = false
            secureTextField.isSecureTextEntry = true
            
            // Find the secure subview (usually _UITextFieldCanvasView or similar)
            // We search recursively for a view that isn't a UITextField but is a system view
            func findSecureView(in view: UIView) -> UIView? {
                let className = type(of: view).description()
                if className.contains("Canvas") || className.contains("Layer") || className.contains("Layout") {
                    return view
                }
                for subview in view.subviews {
                    if let found = findSecureView(in: subview) {
                        return found
                    }
                }
                return nil
            }

            guard let container = findSecureView(in: secureTextField) else {
                print("❌ ScreenPrivacy: Could not find secure container in UITextField")
                return
            }
            
            print("🛡️ ScreenPrivacy: Found secure container: \(type(of: container))")
            secureContainer = container
            container.isUserInteractionEnabled = true
        }

        guard let container = secureContainer else { return }

        // We want to wrap the window's main content (usually the first subview)
        // BUT we must be careful not to create infinite loops or break layout.
        
        // A safer way for multiple views is to move all existing subviews into the container
        // except the container itself.
        for subview in window.subviews {
            if subview != container && subview != secureTextField {
                container.addSubview(subview)
                subview.frame = container.bounds
                subview.autoresizingMask = [.flexibleWidth, .flexibleHeight]
            }
        }

        if !window.subviews.contains(container) {
            window.addSubview(container)
            container.frame = window.bounds
            container.autoresizingMask = [.flexibleWidth, .flexibleHeight]
        }
        
        print("✅ ScreenPrivacy: Secure container active")
    }

    static func removeSecureProtection(from window: UIWindow) {
        print("🔓 ScreenPrivacy: Removing secure protection from window")
        
        guard let container = secureContainer else { return }
        
        // Move all subviews back to the window
        for subview in container.subviews {
            window.addSubview(subview)
            subview.frame = window.bounds
            subview.autoresizingMask = [.flexibleWidth, .flexibleHeight]
        }
        
        container.removeFromSuperview()
        print("✅ ScreenPrivacy: Secure container removed")
    }

    // MARK: - Bridge Functions

    class Init: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            ScreenPrivacyFunctions.setup()
            return ["success": true]
        }
    }

    class Enable: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            print("📝 ScreenPrivacy.Enable called")
            ScreenPrivacyFunctions.isPrivacyEnabled = true
            ScreenPrivacyFunctions.updateProtectionState()
            ScreenPrivacyFunctions.dispatchEvent("MacroTom\\ScreenPrivacy\\Events\\ScreenPrivacyEnabled", [:])
            return ["success": true]
        }
    }

    class Disable: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            print("📝 ScreenPrivacy.Disable called")
            ScreenPrivacyFunctions.isPrivacyEnabled = false
            ScreenPrivacyFunctions.updateProtectionState()
            ScreenPrivacyFunctions.dispatchEvent("MacroTom\\ScreenPrivacy\\Events\\ScreenPrivacyDisabled", [:])
            return ["success": true]
        }
    }

    class IsEnabled: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let enabled = ScreenPrivacyFunctions.isPrivacyEnabled
            print("📝 ScreenPrivacy.IsEnabled: \(enabled)")
            return ["enabled": enabled]
        }
    }

    class IsRecording: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let isCaptured = UIScreen.main.isCaptured
            print("📝 ScreenPrivacy.IsRecording: \(isCaptured)")
            return ["isRecording": isCaptured]
        }
    }
}