//
//  MyOrderPendingCell.swift
//  StarStellar
//
//  Created by Forcepower Infotech Pvt Ltd on 10/01/24.
//  Copyright © 2024 Apple. All rights reserved.
//

import UIKit

class MyOrderPendingCell: UITableViewCell {

    @IBOutlet weak var imgViewProduct: UIImageView!
    @IBOutlet weak var lblProductName: UILabel!
    @IBOutlet weak var lblPointsRequired: UILabel!
    @IBOutlet weak var lblOrderId: UILabel!
    @IBOutlet weak var lblDeliveryDate: UILabel!
    @IBOutlet weak var lblDeliveryStatus: UILabel!
    @IBOutlet weak var btnSupport: UIButton!
    @IBOutlet weak var btnDeliveryConfirmation: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
