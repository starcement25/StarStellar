//
//  TESiteRecommendationCell.swift
//  StarStellar
//
//  Created by Apple on 19/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class TESiteRecommendationCell: UITableViewCell {

    @IBOutlet weak var lblEngineerName: UILabel!
    @IBOutlet weak var lblSiteName: UILabel!
    @IBOutlet weak var lblSiteRecommendationDate: UILabel!
    @IBOutlet weak var viewStatus: FPView!    
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
