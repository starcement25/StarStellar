//
//  BaseTableViewController.swift
//  StarStellar
//
//  Created by Apple on 18/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import Alamofire


class BaseTableViewController: UITableViewController {

    func showToastAlert(_ strMsg: String) -> Void {
        
        let alert = UIAlertController(title:nil, message:strMsg, preferredStyle: .alert)
        present(alert, animated: true)
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(3.0 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            alert.dismiss(animated: true) {
                //Dismissed
            }
        })
    }
    
    func isServerReachable() ->Bool {
        return NetworkReachabilityManager()!.isReachable
    }

}
