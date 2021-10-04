# To reinstall from scratch with all modules needed

```
./vendor/bin/drush site-install farm farm.modules='["farm_land", "farm_activity", "farm_observation", "farm_harvest", "farm_quantity_standard", "farm_role_roles", "farm_api", "farm_ui_dashboard", "farm_login", "farm_ui","farm_migrate","farm_map_mapbox"]'
./vendor/bin/drush en farm_nfa_block_compartment farm_nfa_cfr farm_nfa_forest farm_nfa_natural_forest farm_nfa_plantation_forest farm_nfa_plantation_inventory farm_nfa_zone
```

# Migration

See https://gist.github.com/spalladino/6d981f7b33f6e0afe6bb on how to load a db from outside a docker container.

Migration documentation: https://docs.farmos.org/hosting/migration/

From the nfa_php container:

```
drush migrate:import --group=farm_migrate_config
drush migrate:import --group=farm_migrate_role
drush migrate:import --group=farm_migrate_user
drush migrate:import --group=farm_migrate_file
drush migrate:import --group=farm_migrate_taxonomy
drush migrate:import --group=farm_migrate_area
drush migrate:import --group=farm_migrate_asset_parent
```

# Get farm_forest

```
git clone https://github.com/farmOS/farm_forest.git (into www/web/modules)
git checkout 2.x
```

# Update from farm_nfa repo
```
composer update farmos/farmos --with-dependencies
```
