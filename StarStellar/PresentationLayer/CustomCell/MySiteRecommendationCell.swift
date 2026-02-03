//
//  MySiteRecommendationCell.swift
//  StarStellar
//
//  Created by Apple on 22/07/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class MySiteRecommendationCell: UITableViewCell {
    
    @IBOutlet weak var lblSiteName: UILabel!
    @IBOutlet weak var lblSubmissionDate: UILabel!
    @IBOutlet weak var lblPointsEarned: UILabel!
    @IBOutlet weak var viewStatus: FPView!
    @IBOutlet weak var lblMobile: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}
