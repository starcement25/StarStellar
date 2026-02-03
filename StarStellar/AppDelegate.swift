////
////  AppDelegate.swift
////  StarStellar
////
////  Created by Apple on 18/07/19.
////  Copyright © 2019 Apple. All rights reserved.
////
//
//import UIKit
//import UserNotifications
//import SVProgressHUD
//
//
//@UIApplicationMain
//class AppDelegate: UIResponder, UIApplicationDelegate {
//    
//    var window: UIWindow?
//    let strClientID = "1003435348766-9jvgkqf1phkmfaf59bdssgen4cdktev0.apps.googleusercontent.com"
//    var fltServerAppVersion = 0.0
//    var fltAppVersion = 0.0
//    
//    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?) -> Bool {
//        // Override point for customization after application launch.
//        
//        SVProgressHUD.setDefaultMaskType(SVProgressHUDMaskType.black)
//        
//        //SDKApplicationDelegate.shared.application(application, didFinishLaunchingWithOptions: launchOptions)
//        
//        print("Documents folder path:",StringConstant.Path.Home)
//        preparePushNotifications(for: application)
//        
//        return true
//    }
//    
////    func application(_ application: UIApplication, open url: URL, sourceApplication: String?, annotation: Any) -> Bool {
////        return SDKApplicationDelegate.shared.application(application,
////                                                         open: url,
////                                                         sourceApplication: sourceApplication,
////                                                         annotation: annotation)
////    }
////
////    @available(iOS 9.0, *)
////    func application(_ application: UIApplication,
////                     open url: URL,
////                     options: [UIApplication.OpenURLOptionsKey: Any]) -> Bool {
////        return SDKApplicationDelegate.shared.application(application, open: url, options: options)
////    }
//    
//    
//    
//    func applicationWillResignActive(_ application: UIApplication) {
//        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
//        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
//    }
//    
//    func applicationDidEnterBackground(_ application: UIApplication) {
//        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
//        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
//    }
//    
//    func applicationWillEnterForeground(_ application: UIApplication) {
//        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
//        
//        if self.fltServerAppVersion > self.fltAppVersion {
//            let alertController = UIAlertController(title: StringConstant.kAppName, message: "Update application.", preferredStyle: .alert)
//            let okAction = UIAlertAction(title: "OK", style: .default, handler: { action in
//                    let simple = "https://apps.apple.com/us/app/star-stellar/id1478117733?mt=8"
//                    if let url = URL(string: simple) {
//                        UIApplication.shared.open(url, options:[:], completionHandler: nil)
//                    }
//                })
//            alertController.addAction(okAction)
//            self.window!.rootViewController?.present(alertController, animated: true)
//        }
//        
//    }
//    
//    func applicationDidBecomeActive(_ application: UIApplication) {
//        //AppEventsLogger.activate(application)
//        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
//    }
//    
//    func applicationWillTerminate(_ application: UIApplication) {
//        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
//    }
//    
//    //MARK: - Push Notification
//    
//    func preparePushNotifications(for application: UIApplication){
//        UNUserNotificationCenter.current().requestAuthorization(options: [.badge,.sound,.alert], completionHandler: {granted, error in
//            guard granted else {
//                return
//            }
//            
//            DispatchQueue.main.async {
//                application.registerForRemoteNotifications()
//            }
//            
//        })
//    }
//    
//    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
//        let token = deviceToken.map {String(format: "%02.2hhx", $0)}.joined()
//        UserDefaults.standard.set(token, forKey: "device_token")
//        UserDefaults.standard.synchronize()
//        print(token)
//        
//    }
//    
//    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error: Error) {
//        UserDefaults.standard.set("a3d755ab00a53e50d922303403546d2edc16cad5b3d9357f669f44cf97460754", forKey: "device_token")
//        UserDefaults.standard.synchronize()
//    }
//}
//


//
//  AppDelegate.swift
//  StarStellar
//
//  Updated for Google Sign-In v7+
//

import UIKit
import UserNotifications
import GoogleSignIn
import SVProgressHUD
import FirebaseCore // Optional: if using Firebase for clientID

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate {

    var window: UIWindow?
    let strClientID = "1003435348766-9jvgkqf1phkmfaf59bdssgen4cdktev0.apps.googleusercontent.com"
    var fltServerAppVersion = 0.0
    var fltAppVersion = 0.0

    // MARK: - App Launch

    func application(_ application: UIApplication,
                     didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?) -> Bool {

        SVProgressHUD.setDefaultMaskType(.black)

        print("Documents folder path:", StringConstant.Path.Home)

        preparePushNotifications(for: application)

        // If using Firebase, you can get clientID from Firebase config
        // let clientID = FirebaseApp.app()?.options.clientID ?? strClientID

        return true
    }

    // MARK: - Push Notification Setup

    func preparePushNotifications(for application: UIApplication) {
        UNUserNotificationCenter.current().requestAuthorization(options: [.badge, .sound, .alert]) { granted, error in
            guard granted else { return }
            DispatchQueue.main.async {
                application.registerForRemoteNotifications()
            }
        }
    }

    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
        let token = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
        UserDefaults.standard.set(token, forKey: "device_token")
        UserDefaults.standard.synchronize()
        print("Device Token:", token)
    }

    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error: Error) {
        print("Failed to register for notifications:", error.localizedDescription)
        // Optional fallback token
        UserDefaults.standard.set("fallback_device_token", forKey: "device_token")
    }

    // MARK: - App State Lifecycle

    func applicationWillEnterForeground(_ application: UIApplication) {
        if self.fltServerAppVersion > self.fltAppVersion {
            let alertController = UIAlertController(title: StringConstant.kAppName, message: "Update application.", preferredStyle: .alert)
            let okAction = UIAlertAction(title: "OK", style: .default) { _ in
                if let url = URL(string: "https://apps.apple.com/us/app/star-stellar/id6754081117?mt=8") {
                    UIApplication.shared.open(url, options: [:], completionHandler: nil)
                }
            }
            alertController.addAction(okAction)
            self.window?.rootViewController?.present(alertController, animated: true)
        }
    }

    // MARK: - URL Handling for Google Sign-In

    func application(_ app: UIApplication, open url: URL, options: [UIApplication.OpenURLOptionsKey : Any] = [:]) -> Bool {
        return GIDSignIn.sharedInstance.handle(url)
    }

    // MARK: - Other AppDelegate Methods (Optional)

    func applicationWillResignActive(_ application: UIApplication) { }
    func applicationDidEnterBackground(_ application: UIApplication) { }
    func applicationDidBecomeActive(_ application: UIApplication) { }
    func applicationWillTerminate(_ application: UIApplication) { }
}
