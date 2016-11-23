node('setekhmaseter') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){

sh 'sleep 0.5'
sh 'mkdir -p app/_data/'

sh "curl -o app/_data/guild.json 'https://eu.api.battle.net/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def f = new File( pwd().'/app/_data', 'guild.json' )
def character = new groovy.json.JsonSlurper().parse(f);
//for (i  in $(cat app/_data/guild.json | jq '.members[].character.name' | tr -d '"' )) {
//echo "$i"
//sh "for i in $(cat app/_data/guild.json | jq '.members[].character.name' | tr -d '"' );do;curl -o app/_data/$i.json 'https://eu.api.battle.net/wow/character/Elune/$i?fields=professions&locale=fr_FR&apikey=${env.APIKEY}';sleep 1;done"
//}
stage ('build') {
   sh "vendor/bin/sculpin generate --env=prod"
}

}