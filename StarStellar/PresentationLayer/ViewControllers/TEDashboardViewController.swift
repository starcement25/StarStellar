//
//  TEDashboardViewController.swift
//  StarStellar
//
//  Created by Apple on 16/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit
import SVProgressHUD
import Alamofire
import SwiftyJSON
import MessageUI
import SDWebImage

class TEDashboardViewController: BaseViewController, UITableViewDelegate, UITableViewDataSource, UIGestureRecognizerDelegate, UICollectionViewDelegate, UICollectionViewDataSource, UICollectionViewDelegateFlowLayout,MFMailComposeViewControllerDelegate,UINavigationControllerDelegate {
    
    @IBOutlet weak var lblName: UILabel!
    @IBOutlet weak var lblTopSectionHeader: UILabel!
    @IBOutlet weak var lblTopSectionDescription: UILabel!
    @IBOutlet weak var imgViewTopSection: UIImageView!
    @IBOutlet weak var collViewSlider: UICollectionView!
    @IBOutlet weak var pageControl: UIPageControl!
    @IBOutlet weak var collViewMenu: UICollectionView!
    @IBOutlet var      viewSideMenu: UIView!
    @IBOutlet weak var viewSidePanel: UIView!
    @IBOutlet weak var tblViewSideMenu: UITableView!
    @IBOutlet weak var collViewTopPicks: UICollectionView!
    
    var arrMenuTitle    : [String] = []
    var arrMenuSubtitle : [String] = []
    var arrMenuImages   : [String] = []
    var arrSideMenu     : [String] = []
    
    var arrOfferSlider = [JSON]()
    var intCounterSlider = 0
    var arrTopPicks = [JSON]()
    
    var strWeblink = ""
    var strTitle = ""
    var timer = Timer()
    
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        navigationController?.setNavigationBarHidden(false, animated: true)
        wsShowAppVersion() //Uncomment this before release
        callDashboardContent()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        timer.invalidate()
    }
    
    //MARK: - Initialization Method
    
    func designView() -> Void {
        
        tblViewSideMenu.register(UINib(nibName: "EngineerSideMenuCell", bundle: nil), forCellReuseIdentifier: "cell")
        tblViewSideMenu.separatorColor = UIColor(hexString: "#D72725")
        tblViewSideMenu.tableFooterView = UIView()
        
        let layoutMenu: UICollectionViewFlowLayout = UICollectionViewFlowLayout()
        layoutMenu.scrollDirection = .horizontal
        layoutMenu.minimumLineSpacing = 5.0
        layoutMenu.minimumInteritemSpacing = 0.0
        layoutMenu.sectionInset = UIEdgeInsets(top: 0, left: 5, bottom: 0, right: 5)
        collViewMenu.collectionViewLayout = layoutMenu
        collViewMenu.register(UINib(nibName:"EngineerDashboardCell", bundle: nil), forCellWithReuseIdentifier:"cell")
        
        collViewSlider.register(UINib(nibName:"EngineerDashboardSliderCell", bundle: nil), forCellWithReuseIdentifier:"cell")
        collViewTopPicks.register(UINib(nibName:"TopPicksCell", bundle: nil), forCellWithReuseIdentifier:"cell")
        
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(self.handleTap(_:)))
        tapGesture.delegate = self
        viewSideMenu.addGestureRecognizer(tapGesture)
        
    }
    
    func loadData() -> Void {
        
        arrMenuTitle    = ["Site","Mapped","Update","Gift"];
        arrMenuSubtitle = ["Recommendation","Engineers","Lifting","Catalogue"];
        arrMenuImages   = ["addsite","site","loyalty","loyalty"];
        
        arrSideMenu     = ["About Star Cement","Mapped Engineer","Site Recommendations","Notification","Terms and Condition","Contact Us","Log out"];
        
    }
    
    //MARK: - IBAction's
    
    @IBAction func barButtonMenuClicked(_ sender: UIBarButtonItem) {
        showSideMenu()
    }
    
    @IBAction func barButtonRefreshClicked(_ sender: UIBarButtonItem) {
        callDashboardContent()
    }
    
    @IBAction func barButtonNotificationClicked(_ sender: UIBarButtonItem) {
        performSegue(withIdentifier: "teDashboardToNotification", sender: self)
    }
    
    
    //MARK: - Gesture
    
    @objc func handleTap(_ sender: UITapGestureRecognizer? = nil) {
        hideSideMenu()
    }
    
    func gestureRecognizer(_ gestureRecognizer: UIGestureRecognizer, shouldReceive touch: UITouch) -> Bool {
        return touch.view == gestureRecognizer.view
    }
    
    //MARK: - UICollectionView Delegate and Datasource
    
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        if collectionView == collViewMenu {
            return arrMenuImages.count
        }else if collectionView == collViewTopPicks {
            return arrTopPicks.count
        }else{
            return arrOfferSlider.count
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        let cellIdentifier = "cell"
        if collectionView == collViewMenu {
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: cellIdentifier, for: indexPath) as? EngineerDashboardCell
            cell?.imgView.image = UIImage.init(imageLiteralResourceName: arrMenuImages[indexPath.row])
            cell?.lblTitle.text = arrMenuTitle[indexPath.row]
            cell?.lblSubtitle.text = arrMenuSubtitle[indexPath.row]
            return cell!
        }else if collectionView == collViewTopPicks {
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: cellIdentifier, for: indexPath) as? TopPicksCell
            cell?.lblTopPicks.text = arrTopPicks[indexPath.row]["featured_gift_title"].stringValue
            
            let urlString = arrTopPicks[indexPath.row]["featured_gift_image_link"].stringValue.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed)
            
            cell?.imgViewTopPicks.sd_setImage(with: URL(string: urlString ?? ""), placeholderImage: UIImage(named: "image_placeholder"))
            return cell!
        }else{
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: cellIdentifier, for: indexPath) as? EngineerDashboardSliderCell
            cell?.lblTitle.text = arrOfferSlider[indexPath.row]["slider_header_text"].stringValue
            cell?.lblSubtitle.text = arrOfferSlider[indexPath.row]["slider_description_text"].stringValue
            
            cell?.imgViewSlider.sd_setImage(with: URL(string: arrOfferSlider[indexPath.row]["slider_image_link"].stringValue), placeholderImage: UIImage(named: "image_placeholder"))
            return cell!
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath){
        print("-->>:",arrMenuTitle[indexPath.row],arrMenuSubtitle[indexPath.row]);
        
        if collectionView == collViewSlider {
            let dictOffer = arrOfferSlider[indexPath.row].dictionaryValue
            if dictOffer["slider_category"]?.stringValue == "GIFT" {
                performSegue(withIdentifier: "dashboardToGiftCatalogue", sender: self)
            }
            print(dictOffer)
        }else if collectionView == collViewTopPicks {
            performSegue(withIdentifier: "dashboardToGiftCatalogue", sender: self)
        }else{
            
            let strMenuItem = String(format:"%@ %@", arrMenuTitle[indexPath.row],arrMenuSubtitle[indexPath.row]);
            switch strMenuItem {
            case "Site Recommendation":
                performSegue(withIdentifier: "teDashboardToRecommendedSite", sender: self)
                print(strMenuItem)
            case "Mapped Engineers":
                performSegue(withIdentifier: "dashboardToMyEngineers", sender: self)
                print(strMenuItem)
            case "Gift Catalogue":
                giftCataloguePressed()
                // performSegue(withIdentifier: "dashboardToGiftCatalogue", sender: self)
                // print(strMenuItem)
            case "Update Lifting":
                performSegue(withIdentifier: "teDashboardToUpdateLifting", sender: self)
                print(strMenuItem)
            default:
                print("Nothing Selected")
            }
        }
    }

    var isChecked = false
    @objc func giftCataloguePressed() {
        SVProgressHUD.show()
        guard let url = URL(string: "https://starstellar.com/terms_api.php") else { return }
        let task = URLSession.shared.dataTask(with: url) { data, response, error in
            SVProgressHUD.dismiss()
            guard error == nil, let data = data else {
                print("API error:", error?.localizedDescription ?? "")
                return
            }
            do {
                if let json = try JSONSerialization.jsonObject(with: data) as? [String: Any],
                let content = json["content"] as? String,
                let link = json["link"] as? String {
                    DispatchQueue.main.async {
                        self.showPopup(message: content, link: link)
                    }
                }
            } catch {
                print("JSON parse error:", error)
            }
        }
        task.resume()
    }

    @objc func showPopup(message: String, link: String) {
        isChecked = false

        // Background overlay
        let backgroundView = UIView(frame: self.view.bounds)
        backgroundView.backgroundColor = UIColor.black.withAlphaComponent(0.5)
        backgroundView.tag = 1001
        self.view.addSubview(backgroundView)

        // Popup container
        let popupView = UIView()
        popupView.backgroundColor = .white
        popupView.layer.cornerRadius = 12
        popupView.clipsToBounds = true
        popupView.translatesAutoresizingMaskIntoConstraints = false
        backgroundView.addSubview(popupView)

        NSLayoutConstraint.activate([
            popupView.leadingAnchor.constraint(equalTo: backgroundView.leadingAnchor, constant: 24),
            popupView.trailingAnchor.constraint(equalTo: backgroundView.trailingAnchor, constant: -24),
            popupView.centerYAnchor.constraint(equalTo: backgroundView.centerYAnchor),
            popupView.heightAnchor.constraint(lessThanOrEqualTo: backgroundView.heightAnchor, multiplier: 0.75)
        ])

        // Title
        let titleLabel = UILabel()
        titleLabel.text = "Terms & Conditions"
        titleLabel.font = .boldSystemFont(ofSize: 16)
        titleLabel.translatesAutoresizingMaskIntoConstraints = false
        popupView.addSubview(titleLabel)

        // Separator
        let separator = UIView()
        separator.backgroundColor = UIColor.lightGray.withAlphaComponent(0.4)
        separator.translatesAutoresizingMaskIntoConstraints = false
        popupView.addSubview(separator)

        // Scroll view for HTML content
        let scrollView = UIScrollView()
        scrollView.showsVerticalScrollIndicator = true
        scrollView.translatesAutoresizingMaskIntoConstraints = false
        popupView.addSubview(scrollView)

        // HTML content label
        let contentLabel = UILabel()
        contentLabel.numberOfLines = 0
        contentLabel.font = .systemFont(ofSize: 13)
        contentLabel.translatesAutoresizingMaskIntoConstraints = false

        // ✅ Parse HTML content (handles <ul><li> tags)
        if let htmlData = message.data(using: .utf8),
        let attributed = try? NSAttributedString(
            data: htmlData,
            options: [
                .documentType: NSAttributedString.DocumentType.html,
                .characterEncoding: String.Encoding.utf8.rawValue
            ],
            documentAttributes: nil
        ) {
            contentLabel.attributedText = attributed
        } else {
            contentLabel.text = message
        }

        scrollView.addSubview(contentLabel)

        // Checkbox row
        let checkboxButton = UIButton(type: .system)
        checkboxButton.setImage(UIImage(systemName: "square"), for: .normal)
        checkboxButton.tintColor = .black
        checkboxButton.translatesAutoresizingMaskIntoConstraints = false
        checkboxButton.addTarget(self, action: #selector(checkboxTapped(_:)), for: .touchUpInside)
        popupView.addSubview(checkboxButton)

        let checkboxLabel = UILabel()
        checkboxLabel.text = "I accept the terms & conditions"
        checkboxLabel.font = .systemFont(ofSize: 13)
        checkboxLabel.numberOfLines = 0
        checkboxLabel.translatesAutoresizingMaskIntoConstraints = false
        popupView.addSubview(checkboxLabel)

        // View full terms link
        let linkButton = UIButton(type: .system)
        linkButton.setTitle("View full terms", for: .normal)
        linkButton.titleLabel?.font = .systemFont(ofSize: 12)
        linkButton.accessibilityHint = link
        linkButton.translatesAutoresizingMaskIntoConstraints = false
        linkButton.addTarget(self, action: #selector(openTermsLink(_:)), for: .touchUpInside)
        popupView.addSubview(linkButton)

        // Submit button
        let submitButton = UIButton(type: .system)
        submitButton.setTitle("Submit", for: .normal)
        submitButton.backgroundColor = .systemBlue
        submitButton.setTitleColor(.white, for: .normal)
        submitButton.layer.cornerRadius = 8
        submitButton.translatesAutoresizingMaskIntoConstraints = false
        submitButton.addTarget(self, action: #selector(submitTapped), for: .touchUpInside)
        popupView.addSubview(submitButton)

        // Layout constraints
        NSLayoutConstraint.activate([
            // Title
            titleLabel.topAnchor.constraint(equalTo: popupView.topAnchor, constant: 16),
            titleLabel.leadingAnchor.constraint(equalTo: popupView.leadingAnchor, constant: 16),
            titleLabel.trailingAnchor.constraint(equalTo: popupView.trailingAnchor, constant: -16),

            // Separator
            separator.topAnchor.constraint(equalTo: titleLabel.bottomAnchor, constant: 10),
            separator.leadingAnchor.constraint(equalTo: popupView.leadingAnchor),
            separator.trailingAnchor.constraint(equalTo: popupView.trailingAnchor),
            separator.heightAnchor.constraint(equalToConstant: 0.5),

            // Scroll view
            scrollView.topAnchor.constraint(equalTo: separator.bottomAnchor, constant: 8),
            scrollView.leadingAnchor.constraint(equalTo: popupView.leadingAnchor, constant: 16),
            scrollView.trailingAnchor.constraint(equalTo: popupView.trailingAnchor, constant: -16),
            scrollView.heightAnchor.constraint(equalToConstant: 180),

            // Content label inside scroll view
            contentLabel.topAnchor.constraint(equalTo: scrollView.topAnchor),
            contentLabel.leadingAnchor.constraint(equalTo: scrollView.leadingAnchor),
            contentLabel.trailingAnchor.constraint(equalTo: scrollView.trailingAnchor),
            contentLabel.bottomAnchor.constraint(equalTo: scrollView.bottomAnchor),
            contentLabel.widthAnchor.constraint(equalTo: scrollView.widthAnchor), // ✅ forces vertical scroll only

            // Checkbox
            checkboxButton.topAnchor.constraint(equalTo: scrollView.bottomAnchor, constant: 12),
            checkboxButton.leadingAnchor.constraint(equalTo: popupView.leadingAnchor, constant: 16),
            checkboxButton.widthAnchor.constraint(equalToConstant: 24),
            checkboxButton.heightAnchor.constraint(equalToConstant: 24),

            checkboxLabel.centerYAnchor.constraint(equalTo: checkboxButton.centerYAnchor),
            checkboxLabel.leadingAnchor.constraint(equalTo: checkboxButton.trailingAnchor, constant: 8),
            checkboxLabel.trailingAnchor.constraint(equalTo: popupView.trailingAnchor, constant: -16),

            // View full terms link
            linkButton.topAnchor.constraint(equalTo: checkboxButton.bottomAnchor, constant: 4),
            linkButton.leadingAnchor.constraint(equalTo: popupView.leadingAnchor, constant: 14),

            // Submit button
            submitButton.topAnchor.constraint(equalTo: linkButton.bottomAnchor, constant: 8),
            submitButton.leadingAnchor.constraint(equalTo: popupView.leadingAnchor, constant: 16),
            submitButton.trailingAnchor.constraint(equalTo: popupView.trailingAnchor, constant: -16),
            submitButton.heightAnchor.constraint(equalToConstant: 44),
            submitButton.bottomAnchor.constraint(equalTo: popupView.bottomAnchor, constant: -16),
        ])

        // Tap outside to dismiss
        let tapGesture = UITapGestureRecognizer(target: self, action: #selector(dismissPopup(_:)))
        backgroundView.addGestureRecognizer(tapGesture)
    }

    @objc func openTermsLink(_ sender: UIButton) {
        guard let urlString = sender.accessibilityHint,
            let url = URL(string: urlString) else { return }
        UIApplication.shared.open(url)
    }
    
    @objc func openLink(_ sender: UITapGestureRecognizer) {
        if let label = sender.view as? UILabel,
           let link = label.accessibilityHint,
           let url = URL(string: link) {
            UIApplication.shared.open(url)
        }
    }
    
    @objc func checkboxTapped(_ sender: UIButton) {
        isChecked.toggle()
        let imageName = isChecked ? "checkmark.square" : "square"
        sender.setImage(UIImage(systemName: imageName), for: .normal)
    }
    
    @objc func submitTapped() {
        if isChecked {
            self.view.viewWithTag(1001)?.removeFromSuperview() // remove popup
            performSegue(withIdentifier: "dashboardToGiftCatalogue", sender: self)
        } else {
            showToast(message: "Please accept the terms first.")
        }
    }

    @objc func dismissPopup(_ sender: UITapGestureRecognizer) {
        let location = sender.location(in: sender.view)
        if let popupView = sender.view?.subviews.first, !popupView.frame.contains(location) {
            sender.view?.removeFromSuperview()
        }
    }
    
    // Simple toast function
    func showToast(message: String) {
        let toastLabel = UILabel(frame: CGRect(x: self.view.frame.size.width/2 - 100,
                                               y: self.view.frame.size.height-100,
                                               width: 200, height: 35))
        toastLabel.backgroundColor = UIColor.black.withAlphaComponent(0.8)
        toastLabel.textColor = UIColor.white
        toastLabel.textAlignment = .center;
        toastLabel.font = UIFont.systemFont(ofSize: 14)
        toastLabel.text = message
        toastLabel.alpha = 1.0
        toastLabel.layer.cornerRadius = 8;
        toastLabel.clipsToBounds  =  true
        self.view.addSubview(toastLabel)
        UIView.animate(withDuration: 3.0, delay: 0.1, options: .curveEaseOut, animations: {
             toastLabel.alpha = 0.0
        }, completion: {_ in
             toastLabel.removeFromSuperview()
        })
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        if collectionView == collViewMenu {
            let size = CGSize(width: ((UIScreen.main.bounds).size.width - 35) / 4, height: 132)
            return size
        }else if collectionView == collViewTopPicks {
            let size = CGSize(width: (collViewTopPicks.frame.size.width / 3 ) - 2, height: collViewTopPicks.frame.size.height)
            return size
        }else{
            let size = CGSize(width: collViewSlider.frame.size.width, height: collViewSlider.frame.size.height)
            return size
        }
    }
    
    func scrollViewDidEndDecelerating(_ scrollView: UIScrollView) {
        if scrollView == collViewSlider {
            pageControl.currentPage = Int(scrollView.contentOffset.x) / Int(scrollView.frame.width)
        }
    }
    
    //MARK: - UITableView Delegate and DataSource
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrSideMenu.count;
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "cell"
        let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? EngineerSideMenuCell
        cell?.lblMenuItem.text = arrSideMenu[indexPath.row]
        return cell!
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath){
        
        let strSideMenuItem = arrSideMenu[indexPath.row]
        
        switch strSideMenuItem {
        case "About Star Cement":
            print(strSideMenuItem)
            strWeblink = "https://www.starstellar.com/about_weblink.php"
            strTitle = "About"
            performSegue(withIdentifier: "teDashboardToWebView", sender: self)
        case "Terms and Condition":
            strWeblink = "https://www.starstellar.com/tnc_weblink.php"
            strTitle = "Terms & Conditions"
            performSegue(withIdentifier: "teDashboardToWebView", sender: self)
            print(strSideMenuItem)
        case "Contact Us":
            contactUs()
            print(strSideMenuItem)
        case "Mapped Engineer":
            print(strSideMenuItem)
            performSegue(withIdentifier: "dashboardToMyEngineers", sender: self)
        case "Site Recommendations":
            print(strSideMenuItem)
            performSegue(withIdentifier: "teDashboardToRecommendedSite", sender: self)
        case "Notification":
            print(strSideMenuItem)
            performSegue(withIdentifier: "teDashboardToNotification", sender: self)
        case "Log out":
            print(strSideMenuItem)
            logout()
        default:
            print("Nothing Selected")
        }
        
        hideSideMenu()
    }
    
    //MARK: - Segue
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        if segue.identifier == "teDashboardToWebView" {
            let webvc = segue.destination as? WebViewController
            webvc?.strWeblink = strWeblink
            webvc?.title = strTitle
        }
    }
    
    //MARK: - Web - Service
    
    func callDashboardContent() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["te_code"] = Defaults.teCode()
            
            
            SVProgressHUD.show()
            SSParserLayer.callHomeScreenTE(dict, handler: { strStatus, strMessage, dictResponse in
                SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    self.setDashboardData(dict: json)
                    
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
    }
    
    func wsShowAppVersion() -> Void {
        
        if isServerReachable(){
            
            var dict: [String : Any] = [:]
            dict["te_code"] = Defaults.teCode()
            dict["te_registration_id"] = Defaults.deviceToken()
            dict["te_device_type"] = StringConstant.Device.DeviceType
            dict["te_device_id"] = StringConstant.Device.Id
            dict["app_version"] = StringConstant.App.Version
            
            //SVProgressHUD.show()
            SSParserLayer.callShowAppVersion(dict, handler: { [self] strStatus, strMessage, dictResponse in
                //SVProgressHUD.dismiss()
                if (strStatus == "YES") {
                    let json = JSON(dictResponse!)
                    print(json)
                    if json["te_force_logout"].stringValue == "YES" {
                        
                        let alertController = UIAlertController(title: StringConstant.kAppName, message: json["force_logout_message"].stringValue, preferredStyle: .alert)
                        let okAction = UIAlertAction(title: "OK", style: .default, handler: { [self] action in
                            logoutAndClearData()
                        })
                        alertController.addAction(okAction)
                        present(alertController, animated: true, completion: nil)   
                        
                        return
                    }
                    
                    let fltServerVersion = json["ios_app_version"].floatValue
                    let strAppVersion = Bundle.main.infoDictionary?["CFBundleShortVersionString"] as? String
                    let fltAppVersion = Float((strAppVersion! as NSString).floatValue)
                    
                    let appDelegate = UIApplication.shared.delegate as! AppDelegate
                    appDelegate.fltServerAppVersion = Double(fltServerVersion)
                    appDelegate.fltAppVersion = Double(fltAppVersion)
                    
                    print(appDelegate.fltServerAppVersion)
                    print(appDelegate.fltAppVersion)
                    
                    if appDelegate.fltServerAppVersion > appDelegate.fltAppVersion {
                        let alertController = UIAlertController(title: StringConstant.kAppName, message: "Update application.", preferredStyle: .alert)
                        let okAction = UIAlertAction(title: "OK", style: .default, handler: { action in
                            let simple = "https://apps.apple.com/us/app/star-stellar/id6754081117?mt=8"
                            if let url = URL(string: simple) {
                                UIApplication.shared.open(url, options:[:], completionHandler: nil)
                            }
                        })
                        alertController.addAction(okAction)
                        present(alertController, animated: true, completion: nil)
                    }
                }else{
                    self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
                }
            })
            
        }else{
            showToastAlert(StringConstant.kNoInternet)
        }
        
    }
    
    //MARK: - Set Data
    
    func setDashboardData(dict : JSON) -> Void {
        
        arrOfferSlider.removeAll()
        lblName.text = dict["te_name"].stringValue
        lblTopSectionHeader.text = dict["top_section_header_text"].stringValue
        lblTopSectionDescription.text = dict["top_section_description_text"].stringValue
        imgViewTopSection.sd_setImage(with: URL(string: dict["top_section_image_link"].stringValue), placeholderImage: UIImage(named: "gift_placeholder"))
        
        arrOfferSlider = dict["offer_slider_data"].arrayValue
        arrTopPicks = dict["featured_slider_data"].arrayValue
        pageControl.numberOfPages = arrOfferSlider.count
        timer = Timer.scheduledTimer(timeInterval: 5.0, target: self, selector: #selector(changeSliderImage), userInfo: nil, repeats: true)
        RunLoop.current.add(timer, forMode: .common)
        collViewSlider.reloadData()
        collViewTopPicks.reloadData()        
    }
    
    @objc func changeSliderImage() {
        if intCounterSlider < arrOfferSlider.count {
            let idxPath = IndexPath(item: intCounterSlider, section: 0)
            collViewSlider.scrollToItem(at: idxPath, at: .centeredHorizontally, animated: true)
            pageControl.currentPage = intCounterSlider
            intCounterSlider += 1
        } else {
            intCounterSlider = 0
            let idxPath = IndexPath(item: intCounterSlider, section: 0)
            collViewSlider.scrollToItem(at: idxPath, at: .centeredHorizontally, animated: false)
            pageControl.currentPage = intCounterSlider
            intCounterSlider = 1
        }
    }
    
    //MARK: - Side Menu Method
    
    func showSideMenu() -> Void {
        viewSideMenu.frame = CGRect(x: 0, y: 0, width: (UIScreen.main.bounds).size.width, height: (UIScreen.main.bounds).size.height)
        viewSideMenu.backgroundColor = UIColor.black.withAlphaComponent(0.0)
        viewSidePanel.frame = CGRect(x: -viewSidePanel.frame.size.width, y: 0, width: viewSidePanel.frame.size.width, height: (UIScreen.main.bounds).size.height)
        
        UIView.animate(withDuration: 0.1,
                       delay: 0.0,
                       options: UIView.AnimationOptions.curveEaseInOut,
                       animations: { () -> Void in
                        self.viewSideMenu.backgroundColor = UIColor.black.withAlphaComponent(0.5)
                        let window = UIApplication.shared.keyWindow!
                        window.addSubview(self.viewSideMenu);
                        self.viewSidePanel.frame = CGRect(x: 0, y: 0, width: self.viewSidePanel.frame.size.width, height: (UIScreen.main.bounds).size.height)
                       }, completion: { (finished) -> Void in
                        
                       })
    }
    
    func hideSideMenu() -> Void {
        viewSideMenu.frame = CGRect(x: 0, y: 0, width: (UIScreen.main.bounds).size.width, height: (UIScreen.main.bounds).size.height)
        viewSideMenu.backgroundColor = UIColor.black.withAlphaComponent(0.5)
        viewSidePanel.frame = CGRect(x: 0, y: 0, width: viewSidePanel.frame.size.width, height: (UIScreen.main.bounds).size.height)
        
        UIView.animate(withDuration: 0.2,
                       delay: 0.0,
                       options: UIView.AnimationOptions.curveEaseInOut,
                       animations: { () -> Void in
                        self.viewSideMenu.backgroundColor = UIColor.black.withAlphaComponent(0.0)
                        self.viewSidePanel.frame = CGRect(x: -self.viewSidePanel.frame.size.width, y: 0, width: self.viewSidePanel.frame.size.width, height: (UIScreen.main.bounds).size.height)
                       }, completion: { (finished) -> Void in
                        self.viewSideMenu.frame = CGRect(x: -(UIScreen.main.bounds).size.width, y: 0, width: (UIScreen.main.bounds).size.width, height: (UIScreen.main.bounds).size.height)
                       })
    }
    
    //MARK: - MFMailComposer Delegate
    
    func mailComposeController(_ controller: MFMailComposeViewController, didFinishWith result: MFMailComposeResult, error: Error?) {
        
        controller.dismiss(animated: true, completion: nil)
        
    }
    
    //MARK: - Helper Method
    
    func contactUs() -> Void {
        let actionSheet = UIAlertController(title: "Choose an option for contact with us", message: nil, preferredStyle: UIAlertController.Style.actionSheet)
        
        actionSheet.addAction(UIAlertAction(title: "Email", style: UIAlertAction.Style.default, handler: { (action) in
            let email = "starstellar@starcement.co.in"
            
            if MFMailComposeViewController.canSendMail() {
                
                let mailComposeViewController = MFMailComposeViewController()
                mailComposeViewController.mailComposeDelegate = self
                mailComposeViewController.setToRecipients([email])
                mailComposeViewController.setSubject("Subject")
                mailComposeViewController.setMessageBody("I'm using Star Stellar", isHTML: false)
                
                self.present(mailComposeViewController, animated: true, completion: nil)
                
            }
            
        }))
        
        actionSheet.addAction(UIAlertAction(title: "Phone", style: UIAlertAction.Style.default, handler: { (action) in
            let strMobile = "180034534500"
            if let url = URL(string: "tel://\(strMobile)"),
               UIApplication.shared.canOpenURL(url) {
                if #available(iOS 10, *) {
                    UIApplication.shared.open(url, options: [:], completionHandler:nil)
                } else {
                    UIApplication.shared.openURL(url)
                }
            } else {
                self.showToastAlert(StringConstant.kErrorMsg)
            }
        }))
        
        actionSheet.addAction(UIAlertAction(title: "WhatsApp", style: UIAlertAction.Style.default, handler: { (action) in
            
            let strUrl = "whatsapp://send?phone=+917595080005&text=\("")"
            let whatsappURL = URL(string: strUrl)
            if let whatsappURL = whatsappURL {
                if UIApplication.shared.canOpenURL(whatsappURL) {
                    // [[UIApplication sharedApplication] openURL: whatsappURL];
                    UIApplication.shared.open(whatsappURL, options: [:], completionHandler: { flag in
                        
                    })
                }
            }
            
        }))        
        
        
        actionSheet.addAction(UIAlertAction(title: "CANCEL", style: UIAlertAction.Style.cancel, handler: nil))
        present(actionSheet, animated: true, completion: nil)
    } 
    
    func logout() -> Void {
        let alert = UIAlertController(title: StringConstant.kAppName, message: "Do you want to logout?", preferredStyle: UIAlertController.Style.alert)
        alert.addAction(UIAlertAction(title: "NO", style: UIAlertAction.Style.cancel, handler: nil))
        alert.addAction(UIAlertAction(title: "YES", style: UIAlertAction.Style.default, handler: { [self] _ in
            logoutAndClearData()
        }))
        self.present(alert, animated: true, completion: nil)
    }
    
    func logoutAndClearData() -> Void {
        
        UserDefaults.standard.set("",    forKey: "the_te_id")
        UserDefaults.standard.set("",    forKey: "the_te_email")
        UserDefaults.standard.set("",    forKey: "user_type")
        UserDefaults.standard.set("",    forKey: "the_te_mobile_no")
        UserDefaults.standard.set("",    forKey: "te_code")
        UserDefaults.standard.set("",    forKey: "the_te_name")
        UserDefaults.standard.set("",    forKey: "te_profile_image")
        UserDefaults.standard.set("",    forKey: "logged_in_type")
        UserDefaults.standard.set(false, forKey: "logged_in")
        UserDefaults.standard.synchronize()
        
        for controller in self.navigationController!.viewControllers as Array {
            if controller.isKind(of: SplashViewController.self) {
                self.navigationController!.popToViewController(controller, animated: false)
                break
            }
        }
    }
    
}
