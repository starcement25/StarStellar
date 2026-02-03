//
//  NotificationViewController.swift
//  StarStellar
//
//  Created by Apple on 28/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON
import SDWebImage

class NotificationViewController: BaseViewController {
    
    @IBOutlet weak var tblViewNotification: UITableView!
    var intPageNumber = 1
    var strLastUpdateDatetime = ""
    var arrNotification = [JSON]()
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        tblViewNotification.rowHeight = UITableView.automaticDimension
        tblViewNotification.estimatedRowHeight = 74
        
        tblViewNotification.register(UINib(nibName: "NotificationCell", bundle: nil), forCellReuseIdentifier: "cell")        
    }
    
    func loadData() -> Void {
        print("-->>User Type",Defaults.userType())
        if isServerReachable() {
            
            var dict : [String : Any] = [:]
            if Defaults.userType() == "ENGINEER"{
                
                dict["the_engineer_id"] = Defaults.engineerId()
                dict["page_no"] = intPageNumber
                dict["last_update_datetime"] = strLastUpdateDatetime
                
                SVProgressHUD.show()
                SSParserLayer.callNotificationForEngineer(dict) { (strStatus, strMessage, dictResponse) in
                    SVProgressHUD.dismiss()
                    
                    if strStatus == "YES" {
                        self.intPageNumber += 1
                        let json = JSON(dictResponse!)
                        let arr = json["notification_data"].arrayValue
                        self.strLastUpdateDatetime = arr[0]["n_date_time"].stringValue
                        self.arrNotification += arr
                        self.tblViewNotification.reloadData()
                    }else{
                        self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                    }
                }
                
            }else{
                dict["te_code"] = Defaults.teCode()
                dict["page_no"] = intPageNumber
                dict["last_update_datetime"] = strLastUpdateDatetime
                
                SVProgressHUD.show()
                SSParserLayer.callNotificationForTE(dict) { (strStatus, strMessage, dictResponse) in
                    SVProgressHUD.dismiss()
                    if strStatus == "YES" {
                        SVProgressHUD.show()
                        SSParserLayer.callNotificationForEngineer(dict) { (strStatus, strMessage, dictResponse) in
                            SVProgressHUD.dismiss()
                            
                            if strStatus == "YES" {
                                self.intPageNumber += 1
                                let json = JSON(dictResponse!)
                                let arr = json["notification_data"].arrayValue
                                self.arrNotification += arr
                                self.strLastUpdateDatetime = arr[0]["n_date_time"].stringValue
                                self.tblViewNotification.reloadData()
                            }else{
                                self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                            }
                        }
                        let json = JSON(dictResponse!)
                        self.arrNotification += json["notification_data"].arrayValue
                        self.tblViewNotification.reloadData()
                    }else{
                        self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                    }
                }
            }
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "notificationListToDetails" {
            if let indexPath = tblViewNotification.indexPathForSelectedRow{
                let selectedRow = indexPath.row
                let ndvc = segue.destination as! NotificationDetailsViewController
                ndvc.dictNotification = arrNotification[selectedRow]
            }
        }else if segue.identifier == "notificationListToWebview" {
            if let indexPath = tblViewNotification.indexPathForSelectedRow{
                let selectedRow = indexPath.row
                let wvc = segue.destination as! WebViewController
                wvc.strWeblink = arrNotification[selectedRow]["m_image_link"].stringValue
            }
        }
    }
    
}

//MARK: -

extension NotificationViewController : UITableViewDelegate , UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrNotification.count;
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? NotificationCell
        cell?.imgView.sd_setImage(with: URL(string: arrNotification[indexPath.row]["m_image_link"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
        cell?.lblTitle.text = arrNotification[indexPath.row]["m_title"].stringValue
        cell?.lblSubtitle.text = arrNotification[indexPath.row]["m_message"].stringValue
        cell?.lblDatetime.text = arrNotification[indexPath.row]["n_date_time"].stringValue
        return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if arrNotification[indexPath.row]["m_file_type"].stringValue != "PDF" {
            performSegue(withIdentifier: "notificationListToDetails", sender: self)
        }else{
            performSegue(withIdentifier: "notificationListToWebview", sender: self)
        }
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            loadData()
        }
    }
    
    
}
