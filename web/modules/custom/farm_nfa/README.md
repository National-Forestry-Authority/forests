# To reinstall from scratch with all modules needed

```
drush site-install -y farm farm.modules='["farm_land", "farm_activity", "farm_observation", "farm_harvest", "farm_quantity_standard", "farm_role_roles", "farm_api", "farm_ui_dashboard", "farm_login", "farm_ui","farm_migrate","farm_map_mapbox", "farm_report"]'
drush en -y farm_nfa_block_compartment farm_nfa_cfr farm_nfa_forest farm_nfa_natural_forest farm_nfa_plantation_forest farm_nfa_plantation_inventory farm_nfa_zone
```

# Migration

See https://gist.github.com/spalladino/6d981f7b33f6e0afe6bb on how to load a db from outside a docker container.

Migration documentation: https://docs.farmos.org/hosting/migration/

From the nfa_php container:

```
drush migrate:import --group=farm_migrate_config,farm_migrate_role,farm_migrate_user,farm_migrate_file
drush migrate:import --group=farm_migrate_area,farm_migrate_asset_parent
```

# Re-export farm_nfa modules config.

```
drush cde farm_nfa
drush cde farm_nfa_block_compartment
drush cde farm_nfa_cfr
drush cde farm_nfa_forest
drush cde farm_nfa_lb
drush cde farm_nfa_natural_forest
drush cde farm_nfa_plantation_forest
drush cde farm_nfa_plantation_inventory
drush cde farm_nfa_planting
drush cde farm_nfa_zone
```
