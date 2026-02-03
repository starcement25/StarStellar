//
//  EngineerSiteRecommendedVC.swift
//  StarStellar
//
//  Created by Apple on 22/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD

class EngineerSiteRecommendedVC: BaseViewController {

    @IBOutlet var buttonsTab: [UIButton]!
    @IBOutlet weak var tblViewPending: UITableView!
    @IBOutlet weak var tblViewApproved: UITableView!
    @IBOutlet weak var tblViewRejected: UITableView!
    @IBOutlet var arrTableViews: [UITableView]!
    
    var intPagePending = 1
    var intPageApproved = 1
    var intPageRejected = 1
    
    var strPendingMaxDatetime = ""
    var strApprovedMaxDatetime = ""
    var strRejectedMaxDatetime = ""
    
    var arrPendingRecommendation : [JSON] = []
    var arrApprovedRecommendation : [JSON] = []
    var arrRejectedRecommendation : [JSON] = []
    
    var strTotalCountApproved : String = ""
    var strTotalCountPending : String = ""
    var strTotalCountRejected : String = ""
    
    var intSelectedTab = 101
    
    var strEngineerId : String = ""
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        tblViewPending.register(UINib(nibName: "MySiteRecommendationCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewPending.separatorColor = .clear
        
        tblViewApproved.register(UINib(nibName: "MySiteRecommendationCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewApproved.separatorColor = .clear
        
        tblViewRejected.register(UINib(nibName: "MySiteRecommendationCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewRejected.separatorColor = .clear
        
        
        
    }
    
    func loadData() -> Void {
        callPendingRecommendation()
        callApprovedRecommendation()
        callRejectedRecommendation()
    }
    
    //MARK: - IBAction's
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnTabsClicked(_ sender: UIButton) {
        
        for button in buttonsTab {
            button.isSelected = false
        }
        sender.isSelected = true
        intSelectedTab = sender.tag
        
        for tableView in arrTableViews {
            tableView.isHidden = true
            if sender.tag == tableView.tag{
                tableView.isHidden = false
            }
        }
        
    }
    
    //MARK: - Web Service
    
    func callPendingRecommendation() -> Void {
        
        if isServerReachable() {
            //the_engineer_id,page_no,the_max_date_time
            var dict : [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPagePending
            dict["the_max_date_time"] = strPendingMaxDatetime
            
            SVProgressHUD.show()
            SSParserLayer.callShowPendingSiteRecommendation(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intPagePending += 1
                    
                    let json = JSON(dictResponse!)
                    let array = json["pending_recommendation_data"].arrayValue
                    
                    self.strPendingMaxDatetime = array[0]["r_submission_date"].stringValue
                    
                    if self.strTotalCountPending == "" {
                        self.strTotalCountPending = "\(json["tot_count"].intValue)"
                        let btnPending = self.buttonsTab[0]
                        btnPending.setTitle("Pending (\(self.strTotalCountPending))",for: .normal)
                    }
                    
                    self.arrPendingRecommendation += array
                    
                    print("Pending:",self.arrPendingRecommendation)
                    self.tblViewPending.reloadData()
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
                
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func callApprovedRecommendation() -> Void {
        
        if isServerReachable() {
            //the_engineer_id,page_no,the_max_date_time
            var dict : [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPageApproved
            dict["the_max_date_time"] = strApprovedMaxDatetime
            
            SVProgressHUD.show()
            SSParserLayer.callShowApprovedSiteRecommendation(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intPageApproved += 1
                    
                    let json = JSON(dictResponse!)
                    let array = json["approved_recommendation_data"].arrayValue
                    
                    self.strApprovedMaxDatetime = array[0]["r_submission_date"].stringValue
                    
                    if self.strTotalCountApproved == "" {
                        self.strTotalCountApproved = "\(json["tot_count"].intValue)"
                        let btnApproved = self.buttonsTab[1]
                        btnApproved.setTitle("Approved (\(self.strTotalCountApproved))",for: .normal)
                    }
                    
                    self.arrApprovedRecommendation += array
                    
                    print("Approved:",self.arrApprovedRecommendation)
                    self.tblViewApproved.reloadData()
                    
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
                
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    
    func callRejectedRecommendation() -> Void {
        
        if isServerReachable() {
            //the_engineer_id,page_no,the_max_date_time
            var dict : [String : Any] = [:]
            dict["the_engineer_id"] = strEngineerId
            dict["page_no"] = intPageRejected
            dict["the_max_date_time"] = strRejectedMaxDatetime
            
            SVProgressHUD.show()
            SSParserLayer.callShowRejectedSiteRecommendation(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intPageRejected += 1
                    
                    let json = JSON(dictResponse!)
                    let array = json["rejected_recommendation_data"].arrayValue
                    
                    self.strRejectedMaxDatetime = array[0]["r_submission_date"].stringValue
                    
                    if self.strTotalCountRejected == "" {
                        self.strTotalCountRejected = "\(json["tot_count"].intValue)"
                        let btnRejected = self.buttonsTab[2]
                        btnRejected.setTitle("Rejected (\(self.strTotalCountRejected))",for: .normal)
                    }
                    
                    self.arrRejectedRecommendation += array
                    
                    print("Rejected:",self.arrRejectedRecommendation)
                    self.tblViewRejected.reloadData()
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
                
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    
}

extension EngineerSiteRecommendedVC : UITableViewDelegate, UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if tableView == tblViewPending {
            return arrPendingRecommendation.count
        }else if tableView == tblViewApproved {
            return arrApprovedRecommendation.count
        }else{
            return arrRejectedRecommendation.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let cellIdentifier = "cell"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MySiteRecommendationCell
        
        if tableView == tblViewPending {
            
            cell?.lblSiteName.text = arrPendingRecommendation[indexPath.row]["r_site_name"].stringValue
            cell?.lblSubmissionDate.text = arrPendingRecommendation[indexPath.row]["r_submission_date_modified"].stringValue
            cell?.lblPointsEarned.text = arrPendingRecommendation[indexPath.row]["point_earned"].stringValue
            
            cell?.viewStatus.backgroundColor = .red
            
        }else if tableView == tblViewApproved {
            
            cell?.lblSiteName.text = arrApprovedRecommendation[indexPath.row]["r_site_name"].stringValue
            cell?.lblSubmissionDate.text = arrApprovedRecommendation[indexPath.row]["r_submission_date_modified"].stringValue
            cell?.lblPointsEarned.text = arrApprovedRecommendation[indexPath.row]["point_earned"].stringValue
            cell?.viewStatus.backgroundColor = .green
            
        }else {
            
            cell?.lblSiteName.text = arrRejectedRecommendation[indexPath.row]["r_site_name"].stringValue
            cell?.lblSubmissionDate.text = arrRejectedRecommendation[indexPath.row]["r_submission_date_modified"].stringValue
            cell?.lblPointsEarned.text = arrRejectedRecommendation[indexPath.row]["point_earned"].stringValue
            cell?.viewStatus.backgroundColor = .orange
            
        }
        
        return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath){
        performSegue(withIdentifier: "engineerSiteListToDetails", sender: self)
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "engineerSiteListToDetails" {
            if intSelectedTab == 101 {
                //pending
                if let indexPath = tblViewPending.indexPathForSelectedRow{
                    let selectedRow = indexPath.row
                    let msdvc = segue.destination as! MySiteDetailsViewController
                    msdvc.dictSite = arrPendingRecommendation[selectedRow]
                }
            }else if intSelectedTab == 102 {
                //approved
                if let indexPath = tblViewApproved.indexPathForSelectedRow{
                    let selectedRow = indexPath.row
                    let msdvc = segue.destination as! MySiteDetailsViewController
                    msdvc.dictSite = arrApprovedRecommendation[selectedRow]
                }
            }else{
                //rejected
                if let indexPath = tblViewRejected.indexPathForSelectedRow{
                    let selectedRow = indexPath.row
                    let msdvc = segue.destination as! MySiteDetailsViewController
                    msdvc.dictSite = arrRejectedRecommendation[selectedRow]
                }
            }
        }
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            if scrollView == tblViewPending {
                callPendingRecommendation()
            }else if scrollView == tblViewApproved {
                callApprovedRecommendation()
            }else{
                callRejectedRecommendation()
            }
        }
    }
    
    
}
