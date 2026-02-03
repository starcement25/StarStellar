platform :ios, '13.0'

target 'StarStellar' do
  use_frameworks!

  pod 'Alamofire'
  pod 'SwiftyJSON', '~> 5.0'
  pod 'SVProgressHUD'
  pod 'SDWebImage', '>= 5.15.0'
  pod 'GoogleSignIn', '~> 7.0'
  pod 'Firebase/Core'
end

post_install do |installer|
  installer.pods_project.targets.each do |target|
    target.build_configurations.each do |config|
      config.build_settings['IPHONEOS_DEPLOYMENT_TARGET'] = '13.0'
    end
    target.build_phases.each do |phase|
      if phase.respond_to?(:shell_path)
        phase.shell_path = '/bin/bash'
      end
    end
  end
end
