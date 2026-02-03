//
//  MySiteRecommendationViewController.swift
//  StarStellar
//
//  Created by Apple on 23/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import SwiftyJSON

class MySiteRecommendationViewController: BaseViewController{
    
    @IBOutlet var btnTabs: [UIButton]!
    @IBOutlet weak var tblViewPending: UITableView!
    @IBOutlet weak var tblViewApproved: UITableView!
    
    var intPagePendingSite = 1
    var intPageApprovedSite = 1
    
    var strPendingMaxDatetime = ""
    var strApprovedMaxDatetime = ""
    
    var arrPendingRecommendation : [JSON] = []
    var arrPendingRecommendationAll : [JSON] = []
    
    var arrApprovedRecommendation : [JSON] = []
    var arrApprovedRecommendationAll : [JSON] = []
    
    var strSelectedTab : String = "APPROVED"
    
    var strTotalCountApproved : String = ""
    var strTotalCountPending : String = ""
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        designView()
        loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        navigationController?.setNavigationBarHidden(false, animated: true)
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        tblViewPending.register(UINib(nibName: "MySiteRecommPendingCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewPending.separatorColor = UIColor.clear
        
        tblViewApproved.register(UINib(nibName: "MySiteRecommendationCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewApproved.separatorColor = UIColor.clear
    }
    
    func loadData() -> Void {
        callShowPendingSiteRecommendation()
        callShowApprovedSiteRecommendation()
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if (segue.identifier == "mySiteListToDetails") {
            if strSelectedTab == "PENDING" {
                if let indexPath = tblViewPending.indexPathForSelectedRow{
                    let selectedRow = indexPath.row
                    let msdvc = segue.destination as! MySiteDetailsViewController
                    msdvc.dictSite = arrPendingRecommendation[selectedRow]
                }
            }else{
                if let indexPath = tblViewApproved.indexPathForSelectedRow{
                    let selectedRow = indexPath.row
                    let msdvc = segue.destination as! MySiteDetailsViewController
                    msdvc.dictSite = arrApprovedRecommendation[selectedRow]
                }
            }
        }
    }
    
    //MARK: - IBAction's
    
    @IBAction func btnTabClicked(_ sender: UIButton) {
        for btn in btnTabs {
            btn.isSelected = false
        }
        sender.isSelected = true
        let array = (sender.tag == 102) ? arrPendingRecommendation : arrApprovedRecommendation
        if array.count == 0 {
            showToastAlert("No record found")
        }
        strSelectedTab = (sender.tag == 101) ? "APPROVED" : "PENDING"
        tblViewApproved.isHidden = (sender.tag == 101) ? false : true
    }
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated:true)
    }
    
    //MARK: - Web-Service
    
    func callShowPendingSiteRecommendation() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["page_no"] = intPagePendingSite
            dict["the_max_date_time"] = strPendingMaxDatetime
            
            SVProgressHUD.show()
            SSParserLayer.callShowPendingSiteRecommendation(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intPagePendingSite += 1
                    
                    let json = JSON(dictResponse!)
                    let array = json["pending_recommendation_data"].arrayValue
                    
                    if self.intPagePendingSite == 2 {
                        self.strPendingMaxDatetime = array[0]["r_submission_date"].stringValue
                    }
                    
                    if self.strTotalCountPending == "" {
                        self.strTotalCountPending = "\(json["tot_count"].intValue)"
                        let btnPending = self.btnTabs[0]
                        btnPending.setTitle("PENDING (\(self.strTotalCountPending))",for: .normal)
                    }
                    
                    self.arrPendingRecommendation += array
                    self.arrPendingRecommendationAll += array
                    
                    print("Pending:",self.arrPendingRecommendation)
                    self.tblViewPending.reloadData()
                    
                }else{
                    //self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            //showToastAlert(StringConstant.kNoInternet)
        }
    }
    
    func callShowApprovedSiteRecommendation() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["the_engineer_id"] = Defaults.engineerId()
            dict["page_no"] = intPageApprovedSite
            dict["the_max_date_time"] = strPendingMaxDatetime
            
            SVProgressHUD.show()
            SSParserLayer.callShowApprovedSiteRecommendation(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    
                    self.intPageApprovedSite += 1
                    
                    let json = JSON(dictResponse!)
                    let array = json["approved_recommendation_data"].arrayValue
                    
                    if self.intPageApprovedSite == 2 {
                        self.strApprovedMaxDatetime = array[0]["r_submission_date"].stringValue
                    }
                    
                    if self.strTotalCountApproved == "" {
                        self.strTotalCountApproved = "\(json["tot_count"].intValue)"
                        let btnApproved = self.btnTabs[1]
                        btnApproved.setTitle("APPROVED (\(self.strTotalCountApproved))",for: .normal)
                        print("-->>",self.strTotalCountApproved)
                    }
                    
                    self.arrApprovedRecommendation += array
                    self.arrApprovedRecommendationAll += array
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
    
}

extension MySiteRecommendationViewController : UITableViewDataSource, UITableViewDelegate {
    
    //MARK: - UITableView Delegate and DataSource
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if tableView == tblViewPending {
            return arrPendingRecommendation.count
        }else{
            return arrApprovedRecommendation.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        if tableView == tblViewPending {
            let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MySiteRecommPendingCell
            cell?.lblSiteName.text = arrPendingRecommendation[indexPath.row]["r_site_name"].stringValue
            cell?.lblSubmissionDate.text = arrPendingRecommendation[indexPath.row]["r_submission_date_modified"].stringValue
            cell?.lblMobile.text = arrPendingRecommendation[indexPath.row]["r_mobile_no"].stringValue
            cell?.viewStatus.backgroundColor = UIColor.white
            return cell ?? UITableViewCell()
        }else{
            let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? MySiteRecommendationCell
            cell?.lblSiteName.text = arrApprovedRecommendation[indexPath.row]["r_site_name"].stringValue
            cell?.lblSubmissionDate.text = arrApprovedRecommendation[indexPath.row]["r_submission_date_modified"].stringValue
            cell?.lblPointsEarned.text = arrApprovedRecommendation[indexPath.row]["point_earned"].stringValue
            cell?.lblMobile.text = arrApprovedRecommendation[indexPath.row]["r_mobile_no"].stringValue
            cell?.viewStatus.backgroundColor = UIColor.green
            return cell ?? UITableViewCell()
        }
    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        
        // UITableView only moves in one direction, y axis
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        
        // Change 10.0 to adjust the distance from bottom
        if maximumOffset - currentOffset <= 10.0 {
            if scrollView == tblViewPending {
                callShowPendingSiteRecommendation()
            }else{
                callShowApprovedSiteRecommendation()
            }
        }
    }
    
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath){
        performSegue(withIdentifier: "mySiteListToDetails", sender: self)
    }
    
}

extension MySiteRecommendationViewController : UISearchBarDelegate {
    
    //MARK: - UISearchBar Delegate
    
    func searchBar(_ searchBar: UISearchBar, textDidChange searchText: String) {
        if strSelectedTab == "APPROVED" {
            arrApprovedRecommendation = arrApprovedRecommendationAll.filter( { $0["r_site_name"].stringValue.range(of: searchText, options: .caseInsensitive) != nil || $0["r_mobile_no"].stringValue.range(of: searchText, options: .caseInsensitive) != nil})
            if searchText.count == 0 {
                arrApprovedRecommendation = arrApprovedRecommendationAll
            }
            tblViewApproved.reloadData()
        }else{
            arrPendingRecommendation = arrPendingRecommendationAll.filter( { $0["r_site_name"].stringValue.range(of: searchText, options: .caseInsensitive) != nil || $0["r_mobile_no"].stringValue.range(of: searchText, options: .caseInsensitive) != nil})
            if searchText.count == 0 {
                arrPendingRecommendation = arrPendingRecommendationAll
            }
            tblViewPending.reloadData()
        }
    }
    
    func searchBarSearchButtonClicked(_ searchBar: UISearchBar) {
        self.view.endEditing(true)
    }
}
