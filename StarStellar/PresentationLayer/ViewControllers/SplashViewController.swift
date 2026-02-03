//
//  SplashViewController.swift
//  StarStellar
//
//  Created by Apple on 18/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class SplashViewController: BaseViewController {
    
    @IBOutlet weak var imgViewSplash: UIImageView!
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        designView()
        loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        navigationController?.setNavigationBarHidden(true, animated: true)
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(2.0 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            
            if Defaults.flagLoggedIn(){
                print("-->>",Defaults.flagLoggedIn())
            }else{
                print("-->>",Defaults.flagLoggedIn())
            }
            
            if Defaults.flagLoggedIn() {
                if Defaults.loggedInType() == "ENGINEER" {
                    self.performSegue(withIdentifier: "splashToEngineerDashboard", sender: self)
                }else{
                    //                    self.showAlert("Coming soon")
                    self.performSegue(withIdentifier: "splashToTEDashboard", sender: self)
                }
                
            }else{
                self.performSegue(withIdentifier: "splashToLogin", sender: self)
            }
        })
        
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        if #available(iOS 15.0, *) {
            let navBarAppearance = UINavigationBarAppearance()
            navBarAppearance.configureWithOpaqueBackground()
            navBarAppearance.backgroundColor = UIColor(hexString: "#D72725")      
            navBarAppearance.titleTextAttributes = [NSAttributedString.Key.foregroundColor: UIColor.white]
            navigationController?.navigationBar.standardAppearance = navBarAppearance
            navigationController?.navigationBar.scrollEdgeAppearance = navBarAppearance
        }
        
        // Setting status bar color throughout the app
        navigationController?.setNavigationBarHidden(true, animated: true)
        
        if #available(iOS 13.0, *) {
            let statusBar = UIView(frame: (UIApplication.shared.keyWindow?.windowScene?.statusBarManager?.statusBarFrame)!)
            statusBar.backgroundColor = UIColor(hexString: "#D72725")
            UIApplication.shared.keyWindow?.addSubview(statusBar)         
        }else{
            guard let statusBarView = UIApplication.shared.value(forKeyPath: "statusBarWindow.statusBar") as? UIView else {
                return
            }
            statusBarView.backgroundColor = UIColor(hexString: "#D72725")
        }
        
        
        var iPhoneX = false
        if #available(iOS 11.0, *) {
            let mainWindow = UIApplication.shared.delegate?.window
            if (mainWindow??.safeAreaInsets.top ?? 0.0) > 24.0 {
                iPhoneX = true
            }
        }
        
        var strImage = "splash"
        
        if iPhoneX {
            strImage = String(format: "%@x.png", strImage)
        } else {
            strImage = String(format: "%@.png", strImage)
        }
        
        imgViewSplash.image = UIImage(imageLiteralResourceName: strImage)
        
        
    }
    
    func loadData() -> Void {
        
    }
}
