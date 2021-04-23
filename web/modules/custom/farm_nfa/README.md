#    pull new farmos/farmos:2.x-dev image
#    docker-compose down
#    backup/remove existing volumes
#    docker-compose up
#    reinstall from scratch
#      admin / admin2xforest
#    reconfig root repo
#      git remote rm origin
#      git remote add origin https://github.com/mstenta/farm_nfa.git
#      git fetch --all
#      git reset origin/2.x
#      git checkout -- .
#    copy settings.local.php from backup
#    in settings.php: uncomment include settings.local.php
#    drush en farm_map_mapbox
#    get farm_forest
#      git clone https://github.com/farmOS/farm_forest.git (into www/web/modules)
#      git checkout 2.x
#    drush en farm_nfa
#    re-run migrations
