import groovy.json.JsonSlurperClassic 


@NonCPS
def jsonParse(def json) {
    new groovy.json.JsonSlurperClassic().parseText(json)
}

node('setekhmaseter') {
   stage('install tools') {

   git 'https://ebarboni@github.com/ebarboni/pourlahorde.git'

   sh "composer install"
}
stage('get data'){

sh 'mkdir -p app/_data/'

sh "curl -o app/_data/guild.json 'https://eu.api.battle.net/wow/guild/Elune/Pour%20la%20horde?fields=achievements%2Cchallenge%2Cmembers%2Cnews&locale=fr_FR&apikey=${env.APIKEY}'"
def f = env.WORKSPACE + '/app/_data/guild.json'

def character = jsonParse(readFile(f))
for (charact  in character.get('members') ) {
 def n =  charact.get('character').get('name').toString()
sh "curl -o app/_data/${n}.json 'https://eu.api.battle.net/wow/character/Elune/${n}?fields=professions,items,statistics,progression,audit,talents,achievements,reputation&locale=fr_FR&apikey=${env.APIKEY}'"
sleep 1
}
}
stage ('build') {
   sh './vendor/bin/sculpin generate --env=prod'
}
stage ('deploy') {
   sh "tar -zcvf /tmp/site.tar.gz output_prod/"
}
}