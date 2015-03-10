/*
 * Example of editable comment in the preview/template
  <xsl:template match="comment">
	<xsl:variable name="sid">
		<xsl:choose>
			<xsl:when test="../../description/wrapper_id!=''">
				<xsl:value-of select="../../../../../id_db" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="../../../../id_db" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="rid">
		<xsl:choose>
			<xsl:when test="../../description/wrapper_id!=''">
				<xsl:value-of select="../../description/wrapper_id" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="../../id_db" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:variable name="ebid" select="subject" />
	<xsl:variable name="epid" select="component" />

	<div id="text{$rid}-{$sid}-{$ebid}-{$epid}-0">
		<xsl:copy-of select="text/value/*"/>
	</div>

	<span class="clicktowrite" onclick="clickToEditComment({$sid},{$rid},'{$ebid}','{$epid}','0','{$rid}-{$sid}-{$ebid}-{$epid}-0');" name="Write"></span>

  </xsl:template>
 */
//opens the comment writer window from a template
function clickToEditComment(sid,rid,bid,pid,entryn,openId){
	var helperurl="reportbook/httpscripts/newcomment_writer.php";
	var getvars="sid="+sid+"&rid="+rid+"&bid="+bid+"&pid="+pid+"&entryn="+entryn+"&openid="+openId;
	var src=helperurl+'?'+getvars;
	parent.openModalWindow(src,'');
	}
