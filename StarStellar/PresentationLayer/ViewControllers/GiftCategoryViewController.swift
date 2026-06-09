//
//  GiftCategoryViewController.swift
//  StarStellar
//
//  Created by SBInfowaves on 25/03/26.
//  Copyright © 2026 Apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SVProgressHUD
import Alamofire
import SDWebImage

class GiftCategoryViewController: BaseViewController, StellarPointsDelegate {
    
    @IBOutlet weak var rightBarButtonMyPoints: UIBarButtonItem!
    @IBOutlet weak var collViewGifts: UICollectionView!
    
    var arrGifts = [JSON]()
    var intMyStellarPoints = 0
    var intGiftPageNo = 1
    
    //MARK: - View Life Cycle
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.designView()
        self.loadData()
    }
    
    //MARK: - Initialization Method
    
    func designView() {
        rightBarButtonMyPoints.setTitleTextAttributes(
            [NSAttributedString.Key.font: UIFont.systemFont(ofSize: 12, weight: .semibold)],
            for: .normal
        )
        let layout = UICollectionViewFlowLayout()
        layout.minimumInteritemSpacing = 10
        layout.minimumLineSpacing = 10
        layout.sectionInset = UIEdgeInsets(top: 10, left: 10, bottom: 10, right: 10)
        let totalSpacing: CGFloat = 30
        let itemWidth = (UIScreen.main.bounds.width - totalSpacing) / 2
        layout.itemSize = CGSize(width: itemWidth, height: 140)
        collViewGifts.collectionViewLayout = layout
        collViewGifts.dataSource = self
        collViewGifts.delegate = self
        collViewGifts.backgroundColor = UIColor(hex: "#F5F5F5")
        collViewGifts.register(
            GiftCategoryCell.self,
            forCellWithReuseIdentifier: "categoryCell"
        )
    }
    
    func loadData() {
        callShowMyGifts()
    }
    
    //MARK: - Web Service
    
    func callShowMyGifts() {
        guard isServerReachable() else {
            showToastAlert(StringConstant.kNoInternet)
            return
        }
        
        var dict: [String: Any] = [:]
        dict["the_engineer_id"] = Defaults.engineerId()
        dict["page_no"] = intGiftPageNo
        
        SVProgressHUD.show()
        SSParserLayer.callShowMyGiftsCategory(dict, handler: { strStatus, strMessage, dictResponse in
            SVProgressHUD.dismiss()
            if strStatus == "YES" {
                self.intGiftPageNo += 1
                let json = JSON(dictResponse!)
                self.intMyStellarPoints = json["e_points"].intValue
                self.arrGifts += json["category_data"].arrayValue
                self.collViewGifts.reloadData()
                print(json)
            } else {
                self.showToastAlert(strMessage ?? StringConstant.kErrorMsg)
            }
        })
    }
    
    //MARK: - IBActions
    
    @IBAction func btnBackClicked(_ sender: UIBarButtonItem) {
        navigationController?.popViewController(animated: true)
    }
    
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
    let dict = sender as! JSON

    if segue.identifier == "listToGiftDetails" {
        if let vc = segue.destination as? GiftDetailsViewController {
            vc.strCategoryId      = dict["category_id"].stringValue
            vc.strCategoryName    = dict["category_name"].stringValue
            vc.intMyStellarPoints = intMyStellarPoints
            vc.delegate           = self
        } else if let nav = segue.destination as? UINavigationController,
                  let vc = nav.topViewController as? GiftDetailsViewController {
            vc.strCategoryId      = dict["category_id"].stringValue
            vc.strCategoryName    = dict["category_name"].stringValue
            vc.intMyStellarPoints = intMyStellarPoints
            vc.delegate           = self
        }

    } else if segue.identifier == "listToGiftDetailsTE" {
        if let vc = segue.destination as? TEGiftViewController {
            vc.strCategoryId      = dict["category_id"].stringValue
            vc.strCategoryName    = dict["category_name"].stringValue
            vc.intMyStellarPoints = intMyStellarPoints
        } else if let nav = segue.destination as? UINavigationController,
                  let vc = nav.topViewController as? TEGiftViewController {
            vc.strCategoryId      = dict["category_id"].stringValue
            vc.strCategoryName    = dict["category_name"].stringValue
            vc.intMyStellarPoints = intMyStellarPoints
        }
    }
}
    
    //MARK: - Stellar Points Delegate
    
    func updateStellarPoints(intStellerPoints: Int) {
        intMyStellarPoints = intStellerPoints
    }
}

//MARK: - UICollectionView Delegate and Datasource

extension GiftCategoryViewController: UICollectionViewDataSource, UICollectionViewDelegate, UICollectionViewDelegateFlowLayout {
    
    func collectionView(_ collectionView: UICollectionView,
                        numberOfItemsInSection section: Int) -> Int {
        return arrGifts.count
    }
    
    func collectionView(_ collectionView: UICollectionView,
                        cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        guard let cell = collectionView.dequeueReusableCell(
            withReuseIdentifier: "categoryCell",
            for: indexPath
        ) as? GiftCategoryCell else {
            return UICollectionViewCell()
        }
        
        let dict = arrGifts[indexPath.row]
        let giftCount = dict["gift_count"].intValue
        let categoryName = dict["category_name"].stringValue
        let color = GiftCategoryCell.categoryColors[indexPath.row % GiftCategoryCell.categoryColors.count]
        
        cell.configure(
            categoryName: categoryName,
            giftCount: giftCount,
            color: color
        )
        
        cell.contentView.alpha = giftCount > 0 ? 1.0 : 0.7
        
        return cell
    }
    
    // ✅ On tap — pass full dict via segue using storyboard identifier
    func collectionView(_ collectionView: UICollectionView,
                        didSelectItemAt indexPath: IndexPath) {
        let dict = arrGifts[indexPath.row]
        guard dict["gift_count"].intValue > 0 else {
            showToastAlert("No gifts available in this category yet.")
            return
        }

    let userType = UserDefaults.standard.string(forKey: "logged_in_type") ?? ""
        if userType == "TE" {
            performSegue(withIdentifier: "listToGiftDetailsTE", sender: dict)
        } else {
            performSegue(withIdentifier: "listToGiftDetails", sender: dict)
        }
        // ✅ Send the full dict as sender — category_id and category_name extracted in prepare()
        // performSegue(withIdentifier: "listToGiftDetails", sender: dict)


    }
    
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        let currentOffset = scrollView.contentOffset.y
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height
        if maximumOffset - currentOffset <= 10.0 {
            callShowMyGifts()
        }
    }
}

//MARK: - UIColor Hex Extension

extension UIColor {
    convenience init(hex: String) {
        var hexSanitized = hex.trimmingCharacters(in: .whitespacesAndNewlines)
        hexSanitized = hexSanitized.replacingOccurrences(of: "#", with: "")
        var rgb: UInt64 = 0
        Scanner(string: hexSanitized).scanHexInt64(&rgb)
        let r = CGFloat((rgb & 0xFF0000) >> 16) / 255
        let g = CGFloat((rgb & 0x00FF00) >> 8)  / 255
        let b = CGFloat(rgb & 0x0000FF)          / 255
        self.init(red: r, green: g, blue: b, alpha: 1)
    }
}

//MARK: - GiftCategoryCell

class GiftCategoryCell: UICollectionViewCell {
    
    static let categoryColors: [UIColor] = [
        UIColor(hex: "#4A90D9"),
        UIColor(hex: "#7B68EE"),
        UIColor(hex: "#FF7F7F"),
        UIColor(hex: "#50C878"),
        UIColor(hex: "#FFB347"),
        UIColor(hex: "#87CEEB"),
        UIColor(hex: "#DDA0DD"),
        UIColor(hex: "#F08080"),
    ]
    
    let viewIconBackground = UIView()
    let lblCategoryName    = UILabel()
    let viewBadge          = UIView()
    let lblBadge           = UILabel()
    
    override init(frame: CGRect) {
        super.init(frame: frame)
        setupUI()
    }
    
    required init?(coder: NSCoder) {
        super.init(coder: coder)
        setupUI()
    }
    
    func setupUI() {
        contentView.layer.cornerRadius = 12
        contentView.clipsToBounds = true
        
        viewIconBackground.translatesAutoresizingMaskIntoConstraints = false
        viewIconBackground.clipsToBounds = true
        contentView.addSubview(viewIconBackground)
        
        lblCategoryName.translatesAutoresizingMaskIntoConstraints = false
        lblCategoryName.font = UIFont.systemFont(ofSize: 15, weight: .bold)
        lblCategoryName.textColor = .white
        lblCategoryName.textAlignment = .center
        lblCategoryName.numberOfLines = 3
        lblCategoryName.adjustsFontSizeToFitWidth = true
        lblCategoryName.minimumScaleFactor = 0.7
        viewIconBackground.addSubview(lblCategoryName)
        
        viewBadge.translatesAutoresizingMaskIntoConstraints = false
        viewBadge.layer.cornerRadius = 9
        viewBadge.clipsToBounds = true
        viewIconBackground.addSubview(viewBadge)
        
        lblBadge.translatesAutoresizingMaskIntoConstraints = false
        lblBadge.font = UIFont.systemFont(ofSize: 9, weight: .semibold)
        lblBadge.textAlignment = .center
        lblBadge.textColor = .white
        viewBadge.addSubview(lblBadge)
        
        NSLayoutConstraint.activate([
            
            viewIconBackground.topAnchor.constraint(equalTo: contentView.topAnchor),
            viewIconBackground.leadingAnchor.constraint(equalTo: contentView.leadingAnchor),
            viewIconBackground.trailingAnchor.constraint(equalTo: contentView.trailingAnchor),
            viewIconBackground.bottomAnchor.constraint(equalTo: contentView.bottomAnchor),
            
            lblCategoryName.centerXAnchor.constraint(equalTo: viewIconBackground.centerXAnchor),
            lblCategoryName.centerYAnchor.constraint(equalTo: viewIconBackground.centerYAnchor, constant: -12),
            lblCategoryName.leadingAnchor.constraint(equalTo: viewIconBackground.leadingAnchor, constant: 10),
            lblCategoryName.trailingAnchor.constraint(equalTo: viewIconBackground.trailingAnchor, constant: -10),
            
            viewBadge.bottomAnchor.constraint(equalTo: viewIconBackground.bottomAnchor, constant: -10),
            viewBadge.leadingAnchor.constraint(equalTo: viewIconBackground.leadingAnchor, constant: 10),
            viewBadge.heightAnchor.constraint(equalToConstant: 18),
            
            lblBadge.topAnchor.constraint(equalTo: viewBadge.topAnchor, constant: 2),
            lblBadge.bottomAnchor.constraint(equalTo: viewBadge.bottomAnchor, constant: -2),
            lblBadge.leadingAnchor.constraint(equalTo: viewBadge.leadingAnchor, constant: 8),
            lblBadge.trailingAnchor.constraint(equalTo: viewBadge.trailingAnchor, constant: -8),
        ])
    }
    
    func configure(categoryName: String, giftCount: Int, color: UIColor) {
        viewIconBackground.backgroundColor = color
        lblCategoryName.text = categoryName
        
        if giftCount > 0 {
            viewBadge.backgroundColor = UIColor.white.withAlphaComponent(0.3)
            lblBadge.text = "● \(giftCount) gifts"
            lblBadge.textColor = .white
        } else {
            viewBadge.backgroundColor = UIColor.black.withAlphaComponent(0.2)
            lblBadge.text = "Coming soon"
            lblBadge.textColor = UIColor.white.withAlphaComponent(0.8)
        }
    }
}
